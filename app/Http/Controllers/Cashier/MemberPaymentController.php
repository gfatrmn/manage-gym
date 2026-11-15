<?php

namespace App\Http\Controllers\Cashier;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\Membership;
use App\Models\MembershipPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class MemberPaymentController extends Controller
{
    public function index(Request $request): Response
    {
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
            ->withQueryString()
            ->through(fn (GymMember $member) => [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'phone' => $member->phone,
                'email' => $member->email,
                'checkin_code' => $member->checkin_code,
                'status' => $member->status,
                'expires_at_label' => $member->expires_at?->translatedFormat('d M Y') ?? '-',
                'lifecycle' => RouteHelpers::memberLifecycleStatus($member),
            ]);

        $memberPayments = CashierTransaction::query()
            ->with('member')
            ->where('transaction_group', 'member_payment')
            ->where('transaction_at', '>=', now()->subDay())
            ->latest('transaction_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (CashierTransaction $trx) => [
                'id' => $trx->id,
                'invoice' => $trx->invoice,
                'customer_name' => $trx->customer_name ?? $trx->member?->full_name ?? '-',
                'transaction_type' => $trx->transaction_type,
                'amount_formatted' => RouteHelpers::formatCurrency($trx->amount),
                'payment_method' => strtoupper((string) $trx->payment_method),
                'payment_status' => $trx->payment_status,
                'transaction_at' => $trx->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);

        $packages = MembershipPackage::query()
            ->where('is_active', true)
            ->orderBy('duration_days')
            ->get();

        return Inertia::render('Cashier/MemberPayments', RouteHelpers::buildCashierViewData([
            'pageTitle' => 'Pembayaran Member - Kasir Arena Gym',
            'activePage' => 'cashier.member-payments',
            'members' => $members,
            'memberSearch' => $memberSearch,
            'memberPayments' => $memberPayments,
            'packages' => $packages,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gym_member_id'  => ['required', 'exists:gym_members,id'],
            'package_id'     => ['nullable', 'exists:membership_packages,id'],
            'amount'         => ['nullable', 'integer'],
            'discount'       => ['nullable', 'integer', 'min:0'],
            'paid_amount'    => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris'],
            'notes'          => ['nullable', 'string'],
        ]);

        $member = GymMember::query()->findOrFail($validated['gym_member_id']);
        $package = ! empty($validated['package_id']) ? MembershipPackage::find($validated['package_id']) : null;

        $transactionType = $package ? $package->name : 'Membership 1 Bulan';
        $membershipAmount = $package ? $package->price : 90000;
        $discount = (int) ($validated['discount'] ?? 0);
        $finalAmount = max(0, $membershipAmount - $discount);

        $paidAmount = $validated['payment_method'] === 'qris'
            ? $finalAmount
            : (int) ($validated['paid_amount'] ?? 0);

        if ($validated['payment_method'] === 'cash' && $paidAmount < $finalAmount) {
            return back()
                ->withErrors(['paid_amount' => 'Uang diterima tidak boleh kurang dari nominal pembayaran.'])
                ->withInput();
        }

        $changeAmount = max($paidAmount - $finalAmount, 0);
        $paymentStatus = $validated['payment_method'] === 'cash' ? 'verified' : 'pending';
        $cashierId = session('auth.user_id') ?? session('auth.id') ?? auth()->id();

        DB::transaction(function () use ($paymentStatus, $validated, $member, $package, $transactionType, $membershipAmount, $discount, $finalAmount, $paidAmount, $changeAmount, $cashierId) {
            $newExpiry = null;
            $dates = null;

            if ($package) {
                $dates = RouteHelpers::calculateMembershipDates($member, $package->duration_days);
                $newExpiry = $dates['end_date'];
            } else {
                $newExpiry = RouteHelpers::calculateMembershipRenewalExpiry($member, Carbon::today());
            }

            if ($paymentStatus === 'verified') {
                $memberUpdate = [
                    'payment_method' => $validated['payment_method'],
                    'joined_at'      => $member->joined_at ?? Carbon::today()->toDateString(),
                    'expires_at'     => $newExpiry,
                    'status'         => 'member',
                ];

                if (Schema::hasColumn('gym_members', 'membership_plan')) {
                    $memberUpdate['membership_plan'] = $transactionType;
                }
                if (Schema::hasColumn('gym_members', 'payment_amount')) {
                    $memberUpdate['payment_amount'] = $finalAmount;
                }
                if (Schema::hasColumn('gym_members', 'package_status')) {
                    $memberUpdate['package_status'] = 'active';
                }

                $member->update($memberUpdate);
            }

            $trxPayload = [
                'invoice'           => RouteHelpers::generateInvoice('MP'),
                'gym_member_id'     => $member->id,
                'cashier_id'        => $cashierId,
                'package_id'        => $package?->id,
                'customer_name'     => $member->full_name,
                'transaction_group' => 'member_payment',
                'transaction_type'  => $transactionType,
                'amount'            => $finalAmount,
                'discount'          => $discount,
                'paid_amount'       => $paidAmount,
                'change_amount'     => $changeAmount,
                'payment_method'    => $validated['payment_method'],
                'payment_status'    => $paymentStatus,
                'receipt_status'    => $paymentStatus === 'verified' ? 'ready' : 'pending',
                'transaction_at'    => now(),
                'notes'             => $validated['notes'] ?? null,
            ];

            $trxColumns = Schema::getColumnListing('cashier_transactions');
            $trxPayload = collect($trxPayload)
                ->filter(fn ($value, $key) => in_array($key, $trxColumns, true))
                ->all();

            $transaction = CashierTransaction::query()->create($trxPayload);

            if ($paymentStatus === 'verified' && Schema::hasTable('memberships') && $dates) {
                Membership::create([
                    'member_id'      => $member->id,
                    'package_id'     => $package?->id,
                    'transaction_id' => $transaction->id,
                    'start_date'     => $dates['start_date'],
                    'end_date'       => $dates['end_date'],
                    'price'          => $finalAmount,
                    'status'         => 'active',
                ]);
            }

            ActivityLog::record(
                action: 'renew_membership',
                userId: $cashierId,
                entityType: GymMember::class,
                entityId: $member->id,
                description: "Kasir memperpanjang membership member: {$member->full_name} ({$member->checkin_code}) paket {$transactionType}"
            );
        });

        $redirect = $request->input('return_to') === 'members'
            ? redirect()->route('cashier.members')
            : redirect()->route('cashier.transactions', ['section' => 'member']);

        return $redirect->with('status', 'Pembayaran member berhasil dicatat.');
    }
}

