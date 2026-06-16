<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Cashier – Pembayaran Membership
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/member-payments', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $memberSearch = trim((string) $request->query('member_q', ''));
    $members = GymMember::query()
        ->whereIn('status', ['member', 'daily_pass'])
        ->when($memberSearch !== '', function ($query) use ($memberSearch) {
            $query->where(function ($q) use ($memberSearch) {
                $q->where('full_name', 'like', '%' . $memberSearch . '%')
                    ->orWhere('phone', 'like', '%' . $memberSearch . '%')
                    ->orWhere('email', 'like', '%' . $memberSearch . '%')
                    ->orWhere('checkin_code', 'like', '%' . $memberSearch . '%');
            });
        })
        ->orderByRaw("CASE WHEN status = 'member' THEN 0 ELSE 1 END")
        ->orderBy('full_name')
        ->paginate(10, ['*'], 'members_page')
        ->withQueryString();

    $viewData = RouteHelpers::buildCashierViewData([
        'pageTitle'  => 'Pembayaran Member - Kasir Arena Gym',
        'activePage' => 'cashier.member-payments',
    ]);

    // Pagination dengan limit 10 data per halaman
    $memberPayments = collect($viewData['memberPayments'])
        ->filter(fn (CashierTransaction $t) => $t->transaction_at->gte(now()->subDay()))
        ->values();

    // Konversi collection ke paginated result
    $perPage = 10;
    $page = max((int) $request->query('page', 1), 1);
    $paginatedPayments = new \Illuminate\Pagination\LengthAwarePaginator(
        $memberPayments->forPage($page, $perPage)->values(),
        $memberPayments->count(),
        $perPage,
        $page,
        [
            'path' => route('cashier.member-payments'),
            'query' => $request->query(),
        ]
    );

    return view('cashier.member-payments', array_merge($viewData, [
        'members' => $members,
        'memberSearch' => $memberSearch,
        'memberPayments' => $paginatedPayments,
    ]));
})->name('member-payments');

// ── Store ─────────────────────────────────────────────────────────────────────
Route::post('/member-payments', function (Request $request) {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    $validated = $request->validate([
        'gym_member_id'  => ['required', 'exists:gym_members,id'],
        'amount'         => ['nullable', 'integer'],
        'paid_amount'    => ['nullable', 'integer', 'min:0'],
        'payment_method' => ['required', 'in:cash,qris'],
        'notes'          => ['nullable', 'string'],
    ]);

    $membershipAmount = 90000;
    $paidAmount       = $validated['payment_method'] === 'qris'
        ? $membershipAmount
        : (int) ($validated['paid_amount'] ?? 0);

    if ($validated['payment_method'] === 'cash' && $paidAmount < $membershipAmount) {
        return back()
            ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari nominal pembayaran.'])
            ->withInput();
    }

    $changeAmount     = max($paidAmount - $membershipAmount, 0);
    $paymentStatus    = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
    $member           = GymMember::query()->findOrFail($validated['gym_member_id']);
    $transactionType  = 'Membership 1 Bulan';

    // Jika tunai → langsung perpanjang membership
    if ($paymentStatus === 'verified') {
        $memberUpdate = [
            'payment_method'  => $validated['payment_method'],
            'joined_at'       => $member->joined_at ?? Carbon::today()->toDateString(),
            'expires_at'      => RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today()),
            'status'          => 'member',
        ];

        if (Schema::hasColumn('gym_members', 'membership_plan')) {
            $memberUpdate['membership_plan'] = $transactionType;
        }

        if (Schema::hasColumn('gym_members', 'payment_amount')) {
            $memberUpdate['payment_amount'] = $membershipAmount;
        }

        if (Schema::hasColumn('gym_members', 'package_status')) {
            $memberUpdate['package_status'] = 'active';
        }

        $member->update($memberUpdate);
    }

    CashierTransaction::create([
        'invoice'          => RouteHelpers::generateInvoice('MP'),
        'gym_member_id'    => $member->id,
        'customer_name'    => $member->full_name,
        'transaction_group'=> 'member_payment',
        'transaction_type' => $transactionType,
        'amount'           => $membershipAmount,
        'paid_amount'      => $paidAmount,
        'change_amount'    => $changeAmount,
        'payment_method'   => $validated['payment_method'],
        'payment_status'   => $paymentStatus,
        'receipt_status'   => $paymentStatus === 'verified' ? 'ready' : 'pending',
        'transaction_at'   => now(),
        'notes'            => $validated['notes'] ?? null,
    ]);

    return redirect()->route('cashier.transactions', ['section' => 'member'])
        ->with('status', 'Pembayaran member berhasil dicatat.');
})->name('member-payments.store');
