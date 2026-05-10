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
        'joined_at'     => ['nullable', 'date'],
        'notes'         => ['nullable', 'string'],
    ];
};

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/members', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today         = Carbon::today();
    $memberSection = $request->string('section', 'active')->lower()->value();
    $memberSearch  = trim($request->string('q')->value());

    $membersQuery = GymMember::query()->latest();

    if ($memberSearch !== '') {
        $membersQuery->where(function ($q) use ($memberSearch) {
            $q->where('full_name', 'like', '%' . $memberSearch . '%')
                ->orWhere('email', 'like', '%' . $memberSearch . '%')
                ->orWhere('phone', 'like', '%' . $memberSearch . '%')
                ->orWhere('checkin_code', 'like', '%' . $memberSearch . '%');
        });
    }

    $members = $membersQuery->get();

    $monthlyCheckinHistory = GymCheckin::query()
        ->whereIn('gym_member_id', $members->pluck('id'))
        ->where('verification_status', 'verified')
        ->where('checked_in_at', '>=', now()->subMonth()->startOfDay())
        ->orderByDesc('checked_in_at')
        ->get()
        ->groupBy('gym_member_id');

    $memberProductHistory = CashierTransaction::query()
        ->whereIn('gym_member_id', $members->pluck('id'))
        ->whereNotNull('product_id')
        ->orderByDesc('transaction_at')
        ->get()
        ->groupBy('gym_member_id');

    $activeMembers  = $members->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->gte($today))->values();
    $expiredMembers = $members->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->lt($today))->values();

    return view('admin.members', array_merge(RouteHelpers::pageMeta('members'), [
        'members'               => $members,
        'memberSection'         => $memberSection,
        'memberSearch'          => $memberSearch,
        'activeMembers'         => $activeMembers,
        'expiredMembers'        => $expiredMembers,
        'monthlyCheckinHistory' => $monthlyCheckinHistory,
        'memberProductHistory'  => $memberProductHistory,
    ]));
})->name('members');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/members', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'full_name'     => 'required|string|max:255',
        'email'         => 'nullable|email|unique:gym_members,email',
        'phone'         => 'nullable|string|max:20',
        'joined_at'     => 'required|date',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $joinedAt = Carbon::parse($validated['joined_at']);
    $expiresAt = $joinedAt->copy()->addMonth();

    $memberData = [
        'full_name'    => $validated['full_name'],
        'email'        => $validated['email'],
        'phone'        => $validated['phone'],
        'joined_at'    => $validated['joined_at'],
        'expires_at'   => $expiresAt,
        'status'       => 'active',
        'checkin_code' => 'AGM-' . strtoupper(Str::random(8)),
    ];

    if ($request->hasFile('profile_photo')) {
        $path = $request->file('profile_photo')->store('member-photos', 'public');
        $memberData['profile_photo_path'] = $path;
    }

    GymMember::create($memberData);

    // Kembalikan ->with agar notif muncul di View
    return redirect()->back()->with('status', 'Member baru berhasil ditambahkan!');
})->name('members.store');

// ── Update ────────────────────────────────────────────────────────────────────
Route::put('/members/{member}', function (Request $request, GymMember $member) use ($memberValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate($memberValidationRules($member));

    $data = [
        'full_name' => $validated['full_name'],
        'email'     => $validated['email'],
        'phone'     => $validated['phone'],
        'joined_at' => $validated['joined_at'] ?? $member->joined_at,
    ];

    if ($request->hasFile('profile_photo')) {
        if ($member->profile_photo_path) {
            Storage::disk('public')->delete($member->profile_photo_path);
        }
        $data['profile_photo_path'] = $request->file('profile_photo')->store('member-photos', 'public');
    }

    $member->update($data);

    return redirect()->route('admin.members')->with('status', 'Data member berhasil diperbarui.');
})->name('members.update');

// ── Destroy ───────────────────────────────────────────────────────────────────
Route::delete('/members/{member}', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    if ($member->profile_photo_path) {
        Storage::disk('public')->delete($member->profile_photo_path);
    }

    $member->delete();

    return redirect()->route('admin.members')->with('status', 'Member berhasil dihapus.');
})->name('members.destroy');
