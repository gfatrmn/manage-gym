<?php

use App\Helpers\RouteHelpers;
use App\Models\DailyGuest;
use App\Models\GymCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Check-in & Daily Guest (Non Member)
|--------------------------------------------------------------------------
*/

// ── Halaman check-in kasir ────────────────────────────────────────────────────
Route::get('/checkins', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $section = request()->query('section', 'cashier') === 'nonmember' ? 'nonmember' : 'cashier';

    $nonMemberCheckins = null;

    if ($section === 'nonmember') {
        $nonMemberCheckins = DailyGuest::whereDate('visit_at', today())
            ->orderByDesc('visit_at')
            ->get();
    }

    return view('cashier.checkins', RouteHelpers::buildCashierViewData([
        'pageTitle'          => 'Check-in - Kasir Arena Gym',
        'activePage'         => 'cashier.checkins',
        'sidebarStatusTitle' => 'Check-in Kasir',
        'sidebarStatusNote'  => 'Bantu member check-in dan pantau daftar check-in hari ini.',
        'nonMemberCheckins'  => $nonMemberCheckins,
    ]));
})->name('checkins');

// ── Simpan check-in oleh kasir (member) ───────────────────────────────────────
Route::post('/checkins', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    return RouteHelpers::storeMemberCheckin(
        request: $request,
        actor: 'cashier',
        redirectRoute: 'cashier.checkins',
        redirectParams: [],
    );
})->name('checkins.store');

// ── Simpan daily guest (non member) ──────────────────────────────────────────
Route::post('/checkins/nonmember', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $data = $request->validate([
        'submitted_name'   => ['required', 'string', 'max:255'],
        'submitted_phone'  => ['nullable', 'string', 'max:30'],
        'nominal'          => ['nullable', 'numeric', 'min:0'],
        'payment_method'   => ['nullable', 'string', 'in:cash,qris'],
    ]);

    DailyGuest::create([
        'full_name'      => $data['submitted_name'],
        'phone'          => $data['submitted_phone'] ?? null,
        'payment_amount' => $data['nominal'] ?? 0,
        'payment_method' => $data['payment_method'] ?? 'cash',
        'visit_at'       => now(),
    ]);

    return redirect()
        ->route('cashier.checkins', ['section' => 'nonmember'])
        ->with('status', "Daily Pass atas nama {$data['submitted_name']} berhasil disimpan.");
})->name('checkins.nonmember.store');
