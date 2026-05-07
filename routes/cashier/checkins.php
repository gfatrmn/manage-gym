<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
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

// ── Simpan check-in oleh kasir ────────────────────────────────────────────────
Route::post('/checkins', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    return RouteHelpers::storeMemberCheckin(
        request: $request,
        actor: 'cashier',
        redirectRoute: 'cashier.checkins'
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
