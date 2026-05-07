<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Admin – Manajemen Member
|--------------------------------------------------------------------------
*/

/**
 * Validation rules untuk form member (tambah & edit).
 * Saat edit, email di-exclude dari unique check untuk record yang sama.
 */
$memberValidationRules = function (?GymMember $member = null): array {
    $emailRule = ['nullable', 'email', 'max:255', 'unique:gym_members,email'];

    if ($member) {
        $emailRule = ['nullable', 'email', 'max:255', 'unique:gym_members,email,' . $member->id];
    }

    return [
        'full_name'     => ['required', 'string', 'max:255'],
        'email'         => $emailRule,
        'phone'         => ['nullable', 'string', 'max:30'],
        'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'payment_method'=> ['required', 'in:cash,qris'],
        'joined_at'     => ['nullable', 'date'],
        'notes'         => ['nullable', 'string'],
    ];
};

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/members', function (Request $request) use ($memberValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today         = Carbon::today();
    $memberSection = $request->string('section')->lower()->value();
    $memberSearch  = trim($request->string('q')->value());

    if (! in_array($memberSection, ['active', 'expired'], true)) {
        $memberSection = 'active';
    }

    $membersQuery = GymMember::query()->where('member_status', 'member')->latest();

    if ($memberSearch !== '') {
        $membersQuery->where(function ($q) use ($memberSearch) {
            $q->where('full_name',       'like', '%' . $memberSearch . '%')
                ->orWhere('email',         'like', '%' . $memberSearch . '%')
                ->orWhere('phone',         'like', '%' . $memberSearch . '%')
                ->orWhere('checkin_code',  'like', '%' . $memberSearch . '%')
                ->orWhere('membership_plan','like', '%' . $memberSearch . '%');
        });
    }

    $members = $membersQuery->get();

    $monthlyCheckinHistory = GymCheckin::query()
        ->whereIn('gym_member_id', $members->pluck('id'))
        ->where('verification_status', 'verified')
        ->where('checked_in_at', '>=', now()->subMonth()->startOfDay())
        ->orderByDesc('checked_in_at')
        ->get()
        ->groupBy('gym_member_id')
        ->map(fn ($items) => $items->values());

    $memberProductHistory = CashierTransaction::query()
        ->whereIn('gym_member_id', $members->pluck('id'))
        ->whereNotNull('product_id')
        ->orderByDesc('transaction_at')
        ->get()
        ->groupBy('gym_member_id')
        ->map(fn ($items) => $items->values());

    $activeMembers  = $members->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->gte($today))->values();
    $expiredMembers = $members->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->lt($today))->values();

    return view('admin.members', array_merge(RouteHelpers::pageMeta('members'), [
        'members'              => $members,
        'memberSection'        => $memberSection,
        'memberSearch'         => $memberSearch,
        'activeMembers'        => $activeMembers,
        'expiredMembers'       => $expiredMembers,
        'monthlyCheckinHistory'=> $monthlyCheckinHistory,
        'memberProductHistory' => $memberProductHistory,
    ]));
})->name('members');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/members', function (Request $request) use ($memberValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated  = $request->validate($memberValidationRules());
    $joinedAt   = $validated['joined_at'] ?? now()->toDateString();
    $expiresAt  = RouteHelpers::calculateMonthlyExpiryDate(Carbon::parse($joinedAt));

    $validated = array_merge($validated, [
        'member_status'   => 'member',
        'checkin_code'    => RouteHelpers::generateMemberCheckinCode(),
        'membership_plan' => 'Bulanan',
        'payment_amount'  => 50000,
        'package_status'  => now()->lte(Carbon::parse($expiresAt)) ? 'active' : 'expired',
        'can_check_in'    => false,
        'visit_date'      => null,
        'guest_visit_type'=> null,
        'joined_at'       => $joinedAt,
        'expires_at'      => $expiresAt,
    ]);

    if ($request->hasFile('profile_photo')) {
        $validated['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
    }

    GymMember::create($validated);

    return redirect()->route('admin.members')->with('status', 'Data member berhasil ditambahkan.');
})->name('members.store');

// ── Update ────────────────────────────────────────────────────────────────────
Route::put('/members/{member}', function (Request $request, GymMember $member) use ($memberValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->member_status === 'member', 404);

    $validated = $request->validate($memberValidationRules($member));
    $joinedAt  = $validated['joined_at'] ?? $member->joined_at?->toDateString() ?? now()->toDateString();
    $expiresAt = RouteHelpers::calculateMonthlyExpiryDate(Carbon::parse($joinedAt));

    $validated = array_merge($validated, [
        'member_status'   => 'member',
        'checkin_code'    => $member->checkin_code ?: RouteHelpers::generateMemberCheckinCode(),
        'membership_plan' => 'Bulanan',
        'payment_amount'  => 50000,
        'package_status'  => now()->lte(Carbon::parse($expiresAt)) ? 'active' : 'expired',
        'can_check_in'    => false,
        'visit_date'      => null,
        'guest_visit_type'=> null,
        'joined_at'       => $joinedAt,
        'expires_at'      => $expiresAt,
    ]);

    if ($request->hasFile('profile_photo')) {
        if ($member->profile_photo_path) {
            Storage::disk('public')->delete($member->profile_photo_path);
        }
        $validated['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
    }

    $member->update($validated);

    return redirect()->route('admin.members')->with('status', 'Data member berhasil diperbarui.');
})->name('members.update');

// ── Profile photo: approve ────────────────────────────────────────────────────
Route::post('/members/{member}/profile-photo-requests/approve', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->profile_photo_pending_status === 'pending' && $member->profile_photo_pending_path, 404);

    if ($member->profile_photo_path) {
        Storage::disk('public')->delete($member->profile_photo_path);
    }

    $member->update([
        'profile_photo_path'           => $member->profile_photo_pending_path,
        'profile_photo_pending_path'   => null,
        'profile_photo_pending_status' => 'approved',
    ]);

    return redirect()->route('admin.members')->with('status', 'Permintaan foto profil disetujui.');
})->name('members.profile-photo-requests.approve');

// ── Profile photo: reject ─────────────────────────────────────────────────────
Route::post('/members/{member}/profile-photo-requests/reject', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->profile_photo_pending_status === 'pending' && $member->profile_photo_pending_path, 404);

    Storage::disk('public')->delete($member->profile_photo_pending_path);

    $member->update([
        'profile_photo_pending_path'   => null,
        'profile_photo_pending_status' => 'rejected',
    ]);

    return redirect()->route('admin.members')->with('status', 'Permintaan foto profil ditolak.');
})->name('members.profile-photo-requests.reject');

// ── Destroy ───────────────────────────────────────────────────────────────────
Route::delete('/members/{member}', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->member_status === 'member', 404);

    if ($member->profile_photo_path) {
        Storage::disk('public')->delete($member->profile_photo_path);
    }

    $member->delete();

    return redirect()->route('admin.members')->with('status', 'Data member berhasil dihapus.');
})->name('members.destroy');

// ── Export CSV ────────────────────────────────────────────────────────────────
Route::get('/export/member-data', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $rows = [
        ['Nama', 'Email', 'Telepon', 'Status', 'Paket', 'Status Paket', 'Metode Pembayaran', 'Jumlah Bayar', 'Tanggal Kunjungan', 'Tanggal Gabung', 'Tanggal Berakhir', 'Catatan'],
    ];

    foreach (GymMember::query()->orderBy('full_name')->get() as $member) {
        $rows[] = [
            $member->full_name,
            $member->email,
            $member->phone,
            strtoupper(str_replace('_', ' ', $member->member_status)),
            $member->membership_plan,
            $member->package_status ? strtoupper($member->package_status) : '-',
            $member->payment_method ? strtoupper($member->payment_method) : '-',
            $member->payment_amount ? 'Rp' . number_format($member->payment_amount, 0, ',', '.') : '-',
            optional($member->visit_date)->format('Y-m-d'),
            optional($member->joined_at)->format('Y-m-d'),
            optional($member->expires_at)->format('Y-m-d'),
            $member->notes,
        ];
    }

    $handle = fopen('php://temp', 'r+');
    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }
    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return response($csv, 200, [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="arena-gym-member-data.csv"',
    ]);
})->name('export.member-data');
