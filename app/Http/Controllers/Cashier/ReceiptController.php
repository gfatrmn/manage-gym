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

class ReceiptController extends Controller
{
    public function verifications(): RedirectResponse
    {
        return redirect()->route('cashier.receipts');
    }

    public function confirmVerification(Request $request, int $paymentId): RedirectResponse
    {
        $transaction = CashierTransaction::query()->findOrFail($paymentId);

        DB::transaction(function () use ($transaction) {
            $transaction->update([
                'payment_status' => 'verified',
                'receipt_status' => 'ready',
                'paid_amount' => $transaction->paid_amount ?? $transaction->amount,
                'change_amount' => $transaction->change_amount ?? 0,
            ]);

            if ($transaction->transaction_group === 'member_payment' && $transaction->gym_member_id) {
                $member = GymMember::query()->find($transaction->gym_member_id);

                if ($member) {
                    $months = max(1, (int) ($transaction->quantity ?? 1));
                    $memberUpdate = [
                        'payment_method' => $transaction->payment_method,
                        'joined_at' => $member->joined_at ?? Carbon::today()->toDateString(),
                        'expires_at' => RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today(), $months),
                        'status' => 'member',
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
        });

        $redirectRoute = $request->input('return_to') === 'dashboard'
            ? 'cashier.dashboard'
            : 'cashier.receipts';

        return redirect()->route($redirectRoute)
            ->with('status', "Pembayaran {$transaction->invoice} berhasil diverifikasi.");
    }

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));

        $receiptQueue = CashierTransaction::query()
            ->with(['member', 'product'])
            ->where('payment_method', 'qris')
            ->when($search !== '', fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('invoice', 'like', '%' . $search . '%');
            }))
            ->latest('transaction_at')
            ->paginate(10, ['*'], 'receipt_page')
            ->withQueryString()
            ->through(fn (CashierTransaction $t) => [
                'id' => $t->id,
                'invoice' => $t->invoice,
                'customer_name' => $t->customer_name ?? $t->member?->full_name ?? '-',
                'transaction_group' => $t->transaction_group,
                'transaction_type' => $t->transaction_type,
                'amount_formatted' => RouteHelpers::formatCurrency($t->amount),
                'payment_method' => strtoupper((string) $t->payment_method),
                'payment_status' => $t->payment_status,
                'receipt_status' => $t->receipt_status,
                'transaction_at' => $t->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);

        return Inertia::render('Cashier/Receipts', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Bukti Pembayaran - Kasir Arena Gym',
            'activePage' => 'cashier.receipts',
            'receiptSearch' => $search,
            'receiptQueue' => $receiptQueue,
        ]));
    }

    public function printReceipt(string $invoice): Response
    {
        $receipt = CashierTransaction::query()->with(['member', 'product'])->where('invoice', $invoice)->firstOrFail();
        $receipt->update(['receipt_status' => 'printed']);

        return Inertia::render('Cashier/ReceiptPrint', [
            'pageTitle' => "Cetak Bukti {$invoice}",
            'receipt' => [
                'invoice' => $receipt->invoice,
                'customer_name' => $receipt->customer_name ?? $receipt->member?->full_name ?? '-',
                'transaction_group' => $receipt->transaction_group,
                'transaction_type' => $receipt->transaction_type,
                'quantity' => $receipt->quantity ?? 1,
                'amount' => $receipt->amount,
                'amount_formatted' => RouteHelpers::formatCurrency($receipt->amount),
                'paid_amount_formatted' => RouteHelpers::formatCurrency($receipt->paid_amount ?? $receipt->amount),
                'change_amount_formatted' => RouteHelpers::formatCurrency($receipt->change_amount ?? 0),
                'payment_method' => strtoupper((string) $receipt->payment_method),
                'payment_status' => $receipt->payment_status,
                'receipt_status' => $receipt->receipt_status,
                'transaction_at' => $receipt->transaction_at?->translatedFormat('d M Y, H:i'),
                'notes' => $receipt->notes,
            ],
        ]);
    }
}

