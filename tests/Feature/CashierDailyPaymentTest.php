<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierDailyPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_daily_payment_is_saved_to_transactions_without_forcing_receipt_print(): void
    {
        $this->withSession([
            'auth' => [
                'role' => 'cashier',
                'login' => 'kasir',
            ],
        ])->post(route('cashier.daily-payments.store'), [
            'customer_name' => 'Tamu Tunai',
            'amount' => 20000,
            'paid_amount' => 50000,
            'payment_method' => 'cash',
            'notes' => 'Tidak cetak dulu',
        ])->assertRedirect(route('cashier.transactions', ['section' => 'daily_pass']));

        $this->assertDatabaseHas('cashier_transactions', [
            'customer_name' => 'Tamu Tunai',
            'transaction_group' => 'daily_pass',
            'amount' => 20000,
            'paid_amount' => 50000,
            'change_amount' => 30000,
            'payment_method' => 'cash',
            'payment_status' => 'verified',
            'receipt_status' => 'ready',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'cashier',
                'login' => 'kasir',
            ],
        ])->get(route('cashier.transactions', ['section' => 'daily_pass']))
            ->assertOk()
            ->assertSee('Tamu Tunai')
            ->assertSee('Rp20.000');
    }
}
