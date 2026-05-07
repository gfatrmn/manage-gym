<?php

use App\Helpers\RouteHelpers;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Manajemen Non-Member / Tamu
|--------------------------------------------------------------------------
*/

/** Validation rules untuk form non-member (tamu / daily pass). */
$guestValidationRules = function (?GymMember $member = null): array {
    return [
        'full_name'      => ['required', 'string', 'max:255'],
        'email'          => ['nullable', 'email', 'max:255'],
        'phone'          => ['nullable', 'string', 'max:30'],
        'payment_method' => ['required', 'in:cash,qris'],
        'notes'          => ['nullable', 'string'],
    ];
};

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/non-members', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    return view('admin.non-members', array_merge(RouteHelpers::pageMeta('non-members'), [
        'members' => GymMember::query()->where('member_status', 'non_member')->latest()->get(),
    ]));
})->name('non-members');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/non-members', function (Request $request) use ($guestValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate($guestValidationRules());

    $validated = array_merge($validated, [
        'member_status'   => 'non_member',
        'checkin_code'    => null,
        'membership_plan' => null,
        'package_status'  => null,
        'guest_visit_type'=> null,
        'payment_amount'  => 30000,
        'visit_date'      => now()->toDateString(),
        'can_check_in'    => false,
        'joined_at'       => null,
        'expires_at'      => null,
    ]);

    GymMember::create($validated);

    return redirect()->route('admin.non-members')->with('status', 'Data non-member berhasil ditambahkan.');
})->name('non-members.store');

// ── Update ────────────────────────────────────────────────────────────────────
Route::put('/non-members/{member}', function (Request $request, GymMember $member) use ($guestValidationRules) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->member_status === 'non_member', 404);

    $validated = $request->validate($guestValidationRules($member));

    $validated = array_merge($validated, [
        'member_status'   => 'non_member',
        'membership_plan' => null,
        'package_status'  => null,
        'guest_visit_type'=> null,
        'payment_amount'  => $member->payment_amount ?: 30000,
        'visit_date'      => $member->visit_date?->toDateString() ?: now()->toDateString(),
        'can_check_in'    => false,
        'joined_at'       => null,
        'expires_at'      => null,
    ]);

    $member->update($validated);

    return redirect()->route('admin.non-members')->with('status', 'Data non-member berhasil diperbarui.');
})->name('non-members.update');

// ── Destroy ───────────────────────────────────────────────────────────────────
Route::delete('/non-members/{member}', function (GymMember $member) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    abort_unless($member->member_status === 'non_member', 404);
    $member->delete();

    return redirect()->route('admin.non-members')->with('status', 'Data non-member berhasil dihapus.');
})->name('non-members.destroy');
