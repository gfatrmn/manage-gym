<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
Route::post('/verifications/{paymentId}', function (Request $request, int $paymentId) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $transaction = CashierTransaction::query()->findOrFail($paymentId);

    $transaction->update([
        'payment_status' => 'verified',
        'receipt_status' => 'ready',
        'paid_amount' => $transaction->paid_amount ?? $transaction->amount,
        'change_amount' => $transaction->change_amount ?? 0,
    ]);

    // Jika pembayaran membership → perpanjang masa aktif member
    if ($transaction->transaction_group === 'member_payment' && $transaction->gym_member_id) {
        $member = GymMember::query()->find($transaction->gym_member_id);

        if ($member) {
            $memberUpdate = [
                'payment_method'  => $transaction->payment_method,
                'joined_at'       => $member->joined_at ?? Carbon::today()->toDateString(),
                'expires_at'      => RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today()),
                'status'          => 'member',
            ];

            if (Schema::hasColumn('gym_members', 'membership_plan')) {
                $memberUpdate['membership_plan'] = $transaction->transaction_type;
            }

            if (Schema::hasColumn('gym_members', 'payment_amount')) {
                $memberUpdate['payment_amount'] = $transaction->amount;
            }

            if (Schema::hasColumn('gym_members', 'package_status')) {
                $memberUpdate['package_status'] = 'active';
            }

            $member->update($memberUpdate);
        }
    }

    $redirectRoute = $request->input('return_to') === 'dashboard'
        ? 'cashier.dashboard'
        : 'cashier.receipts';

    return redirect()->route($redirectRoute)
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
    $perPage = 10;
    $receiptPage = max(1, (int) $request->query('receipt_page', 1));
    $receiptQuery = $request->query();
    unset($receiptQuery['receipt_page']);

    $receiptItems = collect($viewData['receiptQueue'])
        ->filter(fn (CashierTransaction $t) => $t->payment_method === 'qris')
        ->when(
            $search !== '',
            fn ($col) => $col->filter(
                fn (CashierTransaction $t) => str_contains(
                    str()->lower($t->customer_name),
                    str()->lower($search)
                )
            )
        )
        ->values();

    $receiptQueue = new LengthAwarePaginator(
        $receiptItems->forPage($receiptPage, $perPage)->values(),
        $receiptItems->count(),
        $perPage,
        $receiptPage,
        [
            'path' => $request->url(),
            'pageName' => 'receipt_page',
            'query' => $receiptQuery,
        ]
    );

    return view('cashier.receipts', array_merge($viewData, [
        'receiptSearch'        => $search,
        'receiptQueue'         => $receiptQueue,
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
