<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Bukti Pembayaran & Verifikasi QRIS
|--------------------------------------------------------------------------
*/

// ── Redirect lama /verifications → /receipts ──────────────────────────────────
Route::get('/verifications', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    return redirect()->route('cashier.receipts');
})->name('verifications');

// ── Verifikasi QRIS ───────────────────────────────────────────────────────────
Route::post('/verifications/{paymentId}', function (int $paymentId) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $transaction = CashierTransaction::query()->findOrFail($paymentId);

    $transaction->update([
        'payment_status' => 'verified',
        'receipt_status' => 'ready',
    ]);

    // Jika pembayaran membership → perpanjang masa aktif member
    if ($transaction->transaction_group === 'member_payment' && $transaction->gym_member_id) {
        $member = GymMember::query()->find($transaction->gym_member_id);

        if ($member) {
            $member->update([
                'membership_plan' => $transaction->transaction_type,
                'payment_method'  => $transaction->payment_method,
                'payment_amount'  => $transaction->amount,
                'joined_at'       => $member->joined_at ?? Carbon::today()->toDateString(),
                'expires_at'      => RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today()),
                'package_status'  => 'active',
            ]);
        }
    }

    return redirect()->route('cashier.receipts')
        ->with('status', "Pembayaran {$transaction->invoice} berhasil diverifikasi.");
})->name('verifications.confirm');

// ── Daftar bukti pembayaran ───────────────────────────────────────────────────
Route::get('/receipts', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Bukti Pembayaran - Kasir Arena Gym',
        'activePage' => 'cashier.receipts',
    ]);

    $search = trim((string) $request->query('q', ''));

    return view('cashier.receipts', array_merge($viewData, [
        'receiptSearch' => $search,
        'receiptQueue'  => collect($viewData['receiptQueue'])
            ->when(
                $search !== '',
                fn ($col) => $col->filter(
                    fn (CashierTransaction $t) => str_contains(
                        str()->lower($t->customer_name),
                        str()->lower($search)
                    )
                )
            )
            ->values(),
    ]));
})->name('receipts');

// ── Cetak bukti pembayaran ────────────────────────────────────────────────────
Route::get('/receipts/{invoice}/print', function (string $invoice) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $receipt = CashierTransaction::query()->where('invoice', $invoice)->firstOrFail();

    $receipt->update(['receipt_status' => 'printed']);

    return view('cashier.receipt-print', [
        'pageTitle' => "Cetak Bukti {$invoice}",
        'receipt'   => $receipt,
    ]);
})->name('receipts.print');
