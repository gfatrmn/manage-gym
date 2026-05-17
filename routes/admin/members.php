<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Admin – Manajemen Member
|--------------------------------------------------------------------------
*/

// Rules untuk validasi agar konsisten
$memberValidationRules = function (?GymMember $member = null): array {
    return [
        'full_name'      => ['required', 'string', 'max:255'],
        'email'          => ['nullable', 'email', 'max:255', 'unique:gym_members,email,' . ($member->id ?? 'NULL')],
        'phone'          => ['nullable', 'string', 'max:30'],
        'profile_photo'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'joined_at'      => ['nullable', 'date'],
        'payment_method' => ['required', 'string'],
        'duration'       => ['nullable', 'integer', 'min:1'],
    ];
};

// ── Index (Updated with Pagination) ──────────────────────────────────────────
Route::get('/members', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $today         = Carbon::today();
    $memberSection = $request->string('section', 'active')->lower()->value();
    $memberSearch  = trim($request->string('q')->value());

    // ── Base query untuk search ──────────────────────────────────────
    $baseQuery = GymMember::query()->latest();

    if ($memberSearch !== '') {
        $baseQuery->where(function ($q) use ($memberSearch) {
            $q->where('full_name', 'like', '%' . $memberSearch . '%')
                ->orWhere('email', 'like', '%' . $memberSearch . '%')
                ->orWhere('phone', 'like', '%' . $memberSearch . '%')
                ->orWhere('checkin_code', 'like', '%' . $memberSearch . '%');
        });
    }

    // ── Counter (selalu dari seluruh data, tidak ikut section) ───────
    $totalActiveCount   = GymMember::where('expires_at', '>=', $today)->count();
    $totalExpiredCount  = GymMember::where('expires_at', '<', $today)->count();
    $totalMembersCount  = GymMember::count();
    $expiringSoonCount  = GymMember::whereBetween('expires_at', [$today, $today->copy()->addDays(7)])->count();

    // ── Data untuk tabel (paginated, ikut search & section) ──────────
    if ($memberSection === 'expired') {
        $activeMembers  = (clone $baseQuery)->where('expires_at', '>=', $today)->paginate(8)->withQueryString();
        $expiredMembers = (clone $baseQuery)->where('expires_at', '<', $today)->paginate(8)->withQueryString();
        $currentItems   = $expiredMembers;
    } else {
        $activeMembers  = (clone $baseQuery)->where('expires_at', '>=', $today)->paginate(8)->withQueryString();
        $expiredMembers = (clone $baseQuery)->where('expires_at', '<', $today)->paginate(8)->withQueryString();
        $currentItems   = $activeMembers;
    }

    return view('admin.members', array_merge(RouteHelpers::pageMeta('members'), [
        'memberSection'      => $memberSection,
        'memberSearch'       => $memberSearch,
        'activeMembers'      => $activeMembers,
        'expiredMembers'     => $expiredMembers,
        'currentItems'       => $currentItems,
        'totalActiveCount'   => $totalActiveCount,
        'totalExpiredCount'  => $totalExpiredCount,
        'totalMembersCount'  => $totalMembersCount,
        'expiringSoonCount'  => $expiringSoonCount,
    ]));
})->name('members');

// ── Store (DIUBAH: Ditambahkan pencatatan transaksi masuk) ───────────────────
Route::post('/members', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $validated = $request->validate([
        'full_name'      => 'required|string|max:255',
        'email'          => 'nullable|email|unique:gym_members,email',
        'phone'          => 'nullable|string|max:20',
        'joined_at'      => 'required|date',
        'payment_method' => 'required|string',
        'profile_photo'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $joinedAt = Carbon::parse($validated['joined_at']);
    $expiresAt = $joinedAt->copy()->addMonth();

    $memberData = [
        'full_name'      => $validated['full_name'],
        'email'          => $validated['email'],
        'phone'          => $validated['phone'],
        'joined_at'      => $joinedAt,
        'expires_at'     => $expiresAt,
        'payment_method' => $validated['payment_method'],
        'status'         => 'active',
        'checkin_code'   => 'AGM-' . strtoupper(Str::random(8)),
    ];

    if ($request->hasFile('profile_photo')) {
        $memberData['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
    }

    // 1. Simpan data member baru ke database
    $member = GymMember::create($memberData);

    // 2. TAMBAHAN: Catat transaksi pendaftaran ke CashierTransaction agar masuk pemasukan hari ini
    $hargaPendaftaran = 90000; // Sesuaikan dengan harga paket pendaftaran Anda

    CashierTransaction::create([
        'invoice'           => 'INV-' . date('Ymd') . strtoupper(Str::random(6)),
        'gym_member_id'     => $member->id,
        'customer_name'     => $member->full_name,
        'transaction_group' => 'membership',
        'transaction_type'  => 'registration', // Tipe: Registrasi Baru
        'amount'            => $hargaPendaftaran,
        'quantity'          => 1,
        'payment_method'    => $member->payment_method,
        'payment_status'    => 'verified', // Set verified agar langsung terjumlah di dashboard
        'receipt_status'    => 'printed',
        'transaction_at'    => now(),
        'notes'             => "Pendaftaran member baru oleh Admin: 1 Bulan",
    ]);

    return redirect()->back()->with('status', 'Member berhasil ditambahkan!');
})->name('members.store');

// ── Update & Perpanjang ────────────────────────────────────────────────────────
Route::put('/members/{member}', function (Request $request, GymMember $member) use ($memberValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $validated = $request->validate($memberValidationRules($member));

    $data = [
        'full_name'      => $validated['full_name'],
        'email'          => $validated['email'] ?? $member->email,
        'phone'          => $validated['phone'] ?? $member->phone,
        'payment_method' => $validated['payment_method'],
        'joined_at'      => $validated['joined_at'] ?? $member->joined_at,
    ];

    if ($request->filled('duration')) {
        $months = (int) $request->duration;
        $baseDate = ($member->expires_at && $member->expires_at->isFuture())
            ? $member->expires_at
            : now();

        $data['expires_at'] = $baseDate->addMonths($months);
        $data['status']     = 'active';

        $hargaPerBulan = 90000;

        CashierTransaction::create([
            'invoice'            => 'INV-' . date('Ymd') . strtoupper(Str::random(6)),
            'gym_member_id'      => $member->id,
            'customer_name'      => $data['full_name'],
            'transaction_group'  => 'membership',
            'transaction_type'   => 'renewal', // Tipe: Perpanjangan
            'amount'             => $months * $hargaPerBulan,
            'quantity'           => $months,
            'payment_method'     => $data['payment_method'],
            'payment_status'     => 'verified',
            'receipt_status'     => 'printed',
            'transaction_at'     => now(),
            'notes'              => "Perpanjangan member oleh Admin: $months Bulan",
        ]);
    }

    if ($request->hasFile('profile_photo')) {
        if ($member->profile_photo_path) Storage::disk('public')->delete($member->profile_photo_path);
        $data['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
    }

    $member->update($data);

    return redirect()->route('admin.members')->with('status', 'Data member dan masa aktif berhasil diperbarui.');
})->name('members.update');

// ── Destroy ───────────────────────────────────────────────────────────────────
Route::delete('/members/{member}', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    if ($member->profile_photo_path) {
        Storage::disk('public')->delete($member->profile_photo_path);
    }

    $member->delete();
    return redirect()->route('admin.members')->with('status', 'Member berhasil dihapus.');
})->name('members.destroy');
