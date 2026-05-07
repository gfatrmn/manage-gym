<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Daily Pass / Non-Member
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/daily-payments', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Daily Non Member - Kasir Arena Gym',
        'activePage' => 'cashier.daily-payments',
    ]);

    return view('cashier.daily-payments', array_merge($viewData, [
        // Tampilkan hanya 24 jam terakhir
        'dailyPayments' => collect($viewData['dailyPayments'])
            ->filter(fn (CashierTransaction $t) => $t->transaction_at->gte(now()->subDay()))
            ->values(),
    ]));
})->name('daily-payments');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/daily-payments', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $validated = $request->validate([
        'customer_name'  => ['required', 'string', 'max:255'],
        'amount'         => ['required', 'integer', 'min:1'],
        'payment_method' => ['required', 'in:cash,qris'],
        'notes'          => ['nullable', 'string'],
    ]);

    $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';

    // Catat sebagai non-member untuk histori kunjungan
    GymMember::create([
        'full_name'       => $validated['customer_name'],
        'member_status'   => 'non_member',
        'membership_plan' => null,
        'package_status'  => null,
        'guest_visit_type'=> 'Daily Pass',
        'payment_method'  => $validated['payment_method'],
        'payment_amount'  => $validated['amount'],
        'can_check_in'    => false,
        'visit_date'      => Carbon::today()->toDateString(),
        'joined_at'       => null,
        'expires_at'      => null,
        'notes'           => $validated['notes'] ?? null,
    ]);

    CashierTransaction::create([
        'invoice'          => RouteHelpers::generateInvoice('DP'),
        'gym_member_id'    => null,
        'customer_name'    => $validated['customer_name'],
        'transaction_group'=> 'daily_pass',
        'transaction_type' => 'Daily Pass',
        'amount'           => $validated['amount'],
        'payment_method'   => $validated['payment_method'],
        'payment_status'   => $paymentStatus,
        'receipt_status'   => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at'   => now(),
        'notes'            => $validated['notes'] ?? null,
    ]);

    return redirect()->route('cashier.daily-payments')
        ->with('status', 'Pembayaran daily pass berhasil dicatat.');
})->name('daily-payments.store');
