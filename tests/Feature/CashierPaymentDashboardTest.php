<?php

namespace Tests\Feature;

use App\Models\CashierTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierPaymentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_dashboard_shows_all_payment_types_and_filters_them(): void
    {
        foreach ([
            ['INV-MEMBER-001', 'Alya Member', 'member_payment', 'Membership 1 Bulan', 90000, 'cash', 'verified'],
            ['INV-DAILY-001', 'Bima Daily', 'daily_pass', 'Daily Pass', 30000, 'cash', 'verified'],
            ['INV-PRODUCT-001', 'Citra Produk', 'product_sale', 'Vitamin C', 95000, 'qris', 'pending'],
            ['INV-OTHER-001', 'Dedi Lainnya', 'other', 'Sewa Loker', 15000, 'cash', 'verified'],
        ] as [$invoice, $customer, $group, $type, $amount, $method, $status]) {
            CashierTransaction::query()->create([
                'invoice' => $invoice,
                'customer_name' => $customer,
                'transaction_group' => $group,
                'transaction_type' => $type,
                'amount' => $amount,
                'paid_amount' => $amount,
                'change_amount' => 0,
                'payment_method' => $method,
                'payment_status' => $status,
                'receipt_status' => $status === 'verified' ? 'ready' : 'pending',
                'transaction_at' => now(),
            ]);
        }

        $session = [
            'auth' => [
                'role' => 'cashier',
                'login' => 'kasir',
            ],
        ];

        $this->withSession($session)
            ->get(route('cashier.transactions'))
            ->assertOk()
            ->assertSee('Alya Member')
            ->assertSee('Bima Daily')
            ->assertSee('Citra Produk')
            ->assertSee('Dedi Lainnya');

        $this->withSession($session)
            ->get(route('cashier.transactions', ['type' => 'product_sale']))
            ->assertOk()
            ->assertSee('Citra Produk')
            ->assertDontSee('Alya Member')
            ->assertDontSee('Bima Daily')
            ->assertDontSee('Dedi Lainnya');
    }
}
