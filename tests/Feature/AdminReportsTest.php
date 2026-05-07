<?php

namespace Tests\Feature;

use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_shows_training_activity_membership_and_financial_data(): void
    {
        $member = GymMember::query()->create([
            'full_name' => 'Alya Fitri',
            'member_status' => 'member',
            'membership_plan' => 'Bulanan',
            'package_status' => 'active',
            'payment_method' => 'qris',
            'payment_amount' => 50000,
            'joined_at' => now()->subDays(5),
            'expires_at' => now()->addDays(25),
        ]);

        $guest = GymMember::query()->create([
            'full_name' => 'Tamu Harian',
            'member_status' => 'non_member',
            'payment_method' => 'cash',
            'payment_amount' => 30000,
            'visit_date' => now(),
        ]);

        GymCheckin::query()->create([
            'gym_member_id' => $member->id,
            'checked_in_at' => now()->startOfMonth()->addDay()->setTime(8, 15),
            'notes' => 'Latihan kardio pagi',
        ]);

        CashierTransaction::query()->create([
            'invoice' => 'INV-MBR-001',
            'gym_member_id' => $member->id,
            'customer_name' => 'Alya Fitri',
            'transaction_group' => 'member_payment',
            'transaction_type' => 'Paket Bulanan',
            'amount' => 50000,
            'payment_method' => 'qris',
            'payment_status' => 'verified',
            'receipt_status' => 'verified',
            'transaction_at' => now()->startOfMonth()->addDays(1),
        ]);

        CashierTransaction::query()->create([
            'invoice' => 'INV-GST-001',
            'gym_member_id' => $guest->id,
            'customer_name' => 'Tamu Harian',
            'transaction_group' => 'daily_pass',
            'transaction_type' => 'Daily Pass',
            'amount' => 30000,
            'payment_method' => 'cash',
            'payment_status' => 'verified',
            'receipt_status' => 'verified',
            'transaction_at' => now()->startOfMonth()->addDays(2),
        ]);

        CashierTransaction::query()->create([
            'invoice' => 'INV-OTH-001',
            'customer_name' => 'Pembeli Minuman',
            'transaction_group' => 'other',
            'transaction_type' => 'Minuman',
            'amount' => 20000,
            'payment_method' => 'qris',
            'payment_status' => 'verified',
            'receipt_status' => 'verified',
            'transaction_at' => now()->startOfMonth()->addDays(3),
        ]);

        ExpenseRecord::query()->create([
            'title' => 'Beli Suplemen Stok',
            'category' => 'inventaris',
            'amount' => 15000,
            'payment_method' => 'cash',
            'expense_date' => now()->startOfMonth()->addDays(4),
            'notes' => 'Restock etalase depan',
        ]);

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('Data detail laporan')
            ->assertSee('Tambah pengeluaran')
            ->assertSee('Simpan Pengeluaran')
            ->assertSee('Alya Fitri')
            ->assertSee('Latihan kardio pagi')
            ->assertSee('Ringkasan membership')
            ->assertSee('Laporan keuangan bulanan')
            ->assertSee('Pendapatan per hari')
            ->assertSee('Breakdown pemasukan')
            ->assertSee('Breakdown pengeluaran')
            ->assertSee('Beli Suplemen Stok')
            ->assertSee('Rp50.000')
            ->assertSee('Rp100.000')
            ->assertSee('Rp15.000')
            ->assertSee('Rp85.000');
    }
}
