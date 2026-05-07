<?php

namespace Tests\Feature;

use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_database_values_for_income_and_expense_stats(): void
    {
        GymMember::query()->create([
            'full_name' => 'Alya Fitri',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'qris',
            'payment_amount' => 50000,
            'joined_at' => now()->subDays(3),
            'expires_at' => now()->addDays(27),
        ]);

        CashierTransaction::query()->create([
            'invoice' => 'INV-DB-001',
            'customer_name' => 'Alya Fitri',
            'transaction_group' => 'member_payment',
            'transaction_type' => 'Paket Bulanan',
            'amount' => 50000,
            'payment_method' => 'qris',
            'payment_status' => 'verified',
            'receipt_status' => 'ready',
            'transaction_at' => now(),
        ]);

        ExpenseRecord::query()->create([
            'title' => 'Beli Air Mineral',
            'category' => 'operasional',
            'amount' => 15000,
            'payment_method' => 'cash',
            'expense_date' => now(),
            'notes' => 'Stok front desk',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pemasukan Hari Ini')
            ->assertSee('Pengeluaran Hari Ini')
            ->assertSee('Laba Bersih Hari Ini')
            ->assertSee('Rp50.000')
            ->assertSee('Rp15.000')
            ->assertSee('Rp35.000');
    }
}
