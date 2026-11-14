<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierProductTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_sell_product_from_master_product_data(): void
    {
        $product = Product::query()->create([
            'name' => 'Vitamin C 1000',
            'category' => 'vitamin',
            'brand' => 'Healthy Co',
            'sku' => 'VITC-001',
            'price' => 95000,
            'stock' => 10,
            'unit' => 'botol',
            'description' => 'Vitamin harian',
            'is_active' => true,
        ]);

        $session = [
            'auth' => [
                'role' => 'cashier',
                'login' => 'cashier',
            ],
        ];

        $this->withSession($session)
            ->post(route('cashier.transactions.store'), [
                'transaction_group' => 'product_sale',
                'customer_name' => 'Budi',
                'product_ids' => [$product->id],
                'quantities' => [
                    $product->id => 2,
                ],
                'payment_method' => 'cash',
                'paid_amount' => 200000,
                'notes' => 'Pembelian vitamin',
            ])
            ->assertRedirect(route('cashier.transactions.products'));

        $this->assertDatabaseHas('cashier_transactions', [
            'product_id' => $product->id,
            'customer_name' => 'Budi',
            'transaction_group' => 'product_sale',
            'transaction_type' => 'Vitamin C 1000',
            'amount' => 190000,
            'paid_amount' => 200000,
            'change_amount' => 10000,
            'quantity' => 2,
            'payment_status' => 'verified',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        $this->withSession($session)
            ->get(route('cashier.transactions.products'))
            ->assertOk()
            ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
                ->component('Cashier/ProductTransactions')
                ->where('products.0.name', 'Vitamin C 1000')
                ->where('productTransactions.0.customer_name', 'Budi')
                ->where('productTransactions.0.amount_formatted', 'Rp190.000')
            );
    }
}
