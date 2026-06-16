<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\MemberHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Check-in & Validasi Pending
|--------------------------------------------------------------------------
*/

// ── Halaman check-in kasir ────────────────────────────────────────────────────
Route::get('/checkins', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    return view('cashier.checkins', RouteHelpers::buildCashierViewData([
        'pageTitle'          => 'Check-in - Kasir Arena Gym',
        'activePage'         => 'cashier.checkins',
        'sidebarStatusTitle' => 'Check-in Kasir',
        'sidebarStatusNote'  => 'Bantu member check-in dan pantau daftar check-in hari ini.',
    ]));
})->name('checkins');

Route::get('/checkins/lookup-member', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'checkin_code' => ['required', 'string', 'max:40'],
    ]);

    $checkinCode = strtoupper(trim($validated['checkin_code']));
    $member = GymMember::query()
        ->where('checkin_code', $checkinCode)
        ->first();

    if (! $member) {
        return response()->json(['message' => 'Member tidak ditemukan.'], 404);
    }

    if (! $member->expires_at || $member->expires_at->lt(now()->startOfDay())) {
        return response()->json(['message' => 'Membership member ini sudah expired.'], 422);
    }

    return response()->json([
        'member' => [
            'id' => $member->id,
            'full_name' => $member->full_name,
            'phone' => $member->phone ?: '-',
            'checkin_code' => $member->checkin_code,
            'expires_at' => $member->expires_at?->format('d M Y') ?: '-',
            'profile_photo_url' => $member->profile_photo_url,
            'profile_initials' => $member->profile_initials,
        ],
    ]);
})->name('checkins.lookup-member');

// ── Simpan check-in oleh kasir ────────────────────────────────────────────────
Route::post('/checkins', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $actor = in_array($request->input('actor'), ['cashier', 'qr_member'], true)
        ? $request->input('actor')
        : 'cashier';

    $redirectParams = $actor === 'qr_member' ? ['section' => 'qr'] : [];

    return RouteHelpers::storeMemberCheckin(
        request: $request,
        actor: $actor,
        redirectRoute: 'cashier.checkins',
        redirectParams: $redirectParams,
    );
})->name('checkins.store');

// ── Verifikasi check-in pending (dari QR self-service) ────────────────────────
Route::post('/checkins/{checkin}/verify', function (GymCheckin $checkin) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    if ($checkin->verification_status !== 'pending') {
        return redirect()->route('cashier.checkins')->with('status', 'Pengajuan check-in ini sudah diproses.');
    }

    $checkin->update([
        'verification_status' => 'verified',
        'checkin_method'      => 'qr_member',
        'verified_at'         => now(),
        'verified_by'         => (string) (session('auth.name') ?? session('auth.login') ?? 'kasir'),
    ]);

    MemberHistory::query()->firstOrCreate(
        [
            'source_type' => GymCheckin::class,
            'source_id' => $checkin->id,
        ],
        [
            'gym_member_id' => $checkin->gym_member_id,
            'history_type'  => 'checkin',
            'occurred_at'   => $checkin->checked_in_at,
            'title'         => 'Check-in member',
            'description'   => 'Check-in melalui QR',
        ]
    );

    return redirect()->route('cashier.checkins')
        ->with('status', "Check-in {$checkin->member?->full_name} berhasil divalidasi kasir.");
})->name('checkins.verify');

// ── Tolak check-in pending ────────────────────────────────────────────────────
Route::post('/checkins/{checkin}/reject', function (GymCheckin $checkin) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    if ($checkin->verification_status !== 'pending') {
        return redirect()->route('cashier.checkins')->with('status', 'Pengajuan check-in ini sudah diproses.');
    }

    $checkin->update([
        'verification_status' => 'rejected',
        'verified_at'         => now(),
        'verified_by'         => (string) (session('auth.name') ?? session('auth.login') ?? 'kasir'),
    ]);

    return redirect()->route('cashier.checkins')
        ->with('status', "Pengajuan check-in {$checkin->member?->full_name} ditolak.");
})->name('checkins.reject');
