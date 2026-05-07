<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Pembayaran Membership
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/member-payments', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Pembayaran Member - Kasir Arena Gym',
        'activePage' => 'cashier.member-payments',
        'members'    => GymMember::query()->where('member_status', 'member')->orderBy('full_name')->get(),
    ]);

    return view('cashier.member-payments', array_merge($viewData, [
        // Tampilkan hanya pembayaran 24 jam terakhir agar halaman tidak overload
        'memberPayments' => collect($viewData['memberPayments'])
            ->filter(fn (CashierTransaction $t) => $t->transaction_at->gte(now()->subDay()))
            ->values(),
    ]));
})->name('member-payments');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/member-payments', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $validated = $request->validate([
        'gym_member_id'  => ['required', 'exists:gym_members,id'],
        'amount'         => ['required', 'integer', 'min:1'],
        'payment_method' => ['required', 'in:cash,qris'],
        'notes'          => ['nullable', 'string'],
    ]);

    $paymentStatus  = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
    $member         = GymMember::query()->findOrFail($validated['gym_member_id']);
    $transactionType= $member->membership_plan ?: 'Membership Bulanan';

    // Jika tunai → langsung perpanjang membership
    if ($paymentStatus === 'verified') {
        $member->update([
            'membership_plan' => $transactionType,
            'payment_method'  => $validated['payment_method'],
            'payment_amount'  => $validated['amount'],
            'joined_at'       => $member->joined_at ?? Carbon::today()->toDateString(),
            'expires_at'      => RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today()),
            'package_status'  => 'active',
        ]);
    }

    CashierTransaction::create([
        'invoice'          => RouteHelpers::generateInvoice('MP'),
        'gym_member_id'    => $member->id,
        'customer_name'    => $member->full_name,
        'transaction_group'=> 'member_payment',
        'transaction_type' => $transactionType,
        'amount'           => $validated['amount'],
        'payment_method'   => $validated['payment_method'],
        'payment_status'   => $paymentStatus,
        'receipt_status'   => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at'   => now(),
        'notes'            => $validated['notes'] ?? null,
    ]);

    return redirect()->route('cashier.member-payments')
        ->with('status', 'Pembayaran member berhasil dicatat.');
})->name('member-payments.store');
