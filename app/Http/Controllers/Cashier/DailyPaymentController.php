<?php

namespace App\Http\Controllers\Cashier;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DailyPaymentController extends Controller
{
    public function index(): Response
    {
        $dailyPayments = CashierTransaction::query()
            ->where('transaction_group', 'daily_pass')
            ->where('transaction_at', '>=', now()->subDay())
            ->latest('transaction_at')
            ->get()
            ->map(fn (CashierTransaction $trx) => [
                'id' => $trx->id,
                'invoice' => $trx->invoice,
                'customer_name' => $trx->customer_name,
                'amount_formatted' => RouteHelpers::formatCurrency($trx->amount),
                'paid_amount_formatted' => RouteHelpers::formatCurrency($trx->paid_amount),
                'change_amount_formatted' => RouteHelpers::formatCurrency($trx->change_amount),
                'payment_method' => strtoupper((string) $trx->payment_method),
                'payment_status' => $trx->payment_status,
                'transaction_at' => $trx->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);

        return Inertia::render('Cashier/DailyPayments', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Daily Pass - Kasir Arena Gym',
            'activePage' => 'cashier.daily-payments',
            'dailyPayments' => $dailyPayments,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'paid_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris'],
            'notes' => ['nullable', 'string'],
        ]);

        $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
        $paidAmount = $validated['payment_method'] === 'qris'
            ? (int) $validated['amount']
            : (int) ($validated['paid_amount'] ?? 0);

        if ($validated['payment_method'] === 'cash' && $paidAmount < (int) $validated['amount']) {
            return back()
                ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari nominal pembayaran.'])
                ->withInput();
        }

        $changeAmount = max($paidAmount - (int) $validated['amount'], 0);

        $transaction = DB::transaction(function () use ($validated, $paidAmount, $changeAmount, $paymentStatus) {
            $dailyPassData = [
                'full_name' => $validated['customer_name'],
                'payment_method' => $validated['payment_method'],
                'joined_at' => null,
                'expires_at' => null,
                'status' => 'daily_pass',
            ];

            foreach ([
                'member_status' => 'daily_pass',
                'membership_plan' => null,
                'package_status' => null,
                'guest_visit_type' => 'Daily Pass',
                'payment_amount' => $validated['amount'],
                'can_check_in' => false,
                'visit_date' => Carbon::today()->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ] as $column => $value) {
                if (Schema::hasColumn('gym_members', $column)) {
                    $dailyPassData[$column] = $value;
                }
            }

            GymMember::query()->create($dailyPassData);

            return CashierTransaction::query()->create([
                'invoice' => RouteHelpers::generateInvoice('DP'),
                'gym_member_id' => null,
                'customer_name' => $validated['customer_name'],
                'transaction_group' => 'daily_pass',
                'transaction_type' => 'Daily Pass',
                'amount' => $validated['amount'],
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'receipt_status' => $paymentStatus === 'verified' ? 'ready' : 'pending',
                'transaction_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        if ($validated['payment_method'] === 'cash') {
            return redirect()->route('cashier.transactions', ['section' => 'daily_pass'])
                ->with('status', "Pembayaran tunai {$transaction->invoice} berhasil dicatat. Struk bisa dicetak kapan saja dari daftar transaksi.");
        }

        return redirect()->route('cashier.dashboard')
            ->with('status', 'Pembayaran QRIS berhasil dicatat. Verifikasi setelah pembayaran terlihat masuk.');
    }
}

