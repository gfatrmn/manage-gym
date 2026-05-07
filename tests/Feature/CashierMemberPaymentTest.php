<?php

namespace Tests\Feature;

use App\Models\CashierTransaction;
use App\Models\GymMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CashierMemberPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_before_expiry_keeps_the_same_membership_cycle_day(): void
    {
        Carbon::setTestNow('2026-05-20 09:00:00');

        $member = GymMember::query()->create([
            'full_name' => 'Member Tanggal 26',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'joined_at' => '2026-04-26',
            'expires_at' => '2026-05-26',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'cashier',
                'login' => 'kasir',
            ],
        ])->post(route('cashier.member-payments.store'), [
            'gym_member_id' => $member->id,
            'transaction_type' => 'Perpanjangan Membership',
            'amount' => 50000,
            'payment_method' => 'cash',
            'notes' => 'Renew lebih awal',
        ])->assertRedirect(route('cashier.member-payments'));

        $member->refresh();

        $this->assertSame('2026-06-26', $member->expires_at?->toDateString());

        $this->assertDatabaseHas('cashier_transactions', [
            'gym_member_id' => $member->id,
            'transaction_group' => 'member_payment',
            'payment_status' => 'verified',
        ]);

        Carbon::setTestNow();
    }

    public function test_pending_member_payment_verification_uses_the_same_cycle_day(): void
    {
        Carbon::setTestNow('2026-05-20 09:00:00');

        $member = GymMember::query()->create([
            'full_name' => 'Member Pending',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'qris',
            'payment_amount' => 50000,
            'joined_at' => '2026-04-26',
            'expires_at' => '2026-05-26',
        ]);

        $transaction = CashierTransaction::query()->create([
            'invoice' => 'MP-TEST-001',
            'gym_member_id' => $member->id,
            'customer_name' => $member->full_name,
            'transaction_group' => 'member_payment',
            'transaction_type' => 'Perpanjangan Membership',
            'amount' => 50000,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'receipt_status' => 'pending',
            'transaction_at' => Carbon::now(),
            'notes' => 'Menunggu verifikasi',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'cashier',
                'login' => 'kasir',
            ],
        ])->post(route('cashier.verifications.confirm', $transaction->id))
            ->assertRedirect(route('cashier.receipts'));

        $member->refresh();
        $transaction->refresh();

        $this->assertSame('2026-06-26', $member->expires_at?->toDateString());
        $this->assertSame('verified', $transaction->payment_status);

        Carbon::setTestNow();
    }
}
