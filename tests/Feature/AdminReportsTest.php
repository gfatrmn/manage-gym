<?php

namespace Tests\Feature;

use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\Product;
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
            ->assertSee('Tambah pengeluaran')
            ->assertSee('Simpan Pengeluaran')
            ->assertSee('Laporan Member')
            ->assertSee('Laporan Keuangan')
            ->assertSee('Laporan Kehadiran')
            ->assertSee('Rp50.000')
            ->assertSee('Rp100.000')
            ->assertSee('Rp15.000')
            ->assertSee('Rp85.000');

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.reports.show', [
            'reportSlug' => 'laporan-kehadiran',
            'detail_month' => now()->format('Y-m'),
        ]))
            ->assertOk()
            ->assertSee('Alya Fitri')
            ->assertSee('Latihan kardio pagi');

        $this->withSession([
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ])->get(route('admin.reports.show', [
            'reportSlug' => 'laporan-keuangan',
            'detail_month' => now()->format('Y-m'),
        ]))
            ->assertOk()
            ->assertSee('Sumber Pendapatan')
            ->assertSee('Riwayat Pemasukan')
            ->assertSee('Riwayat Pengeluaran')
            ->assertSee('Beli Suplemen Stok');
    }

    public function test_stock_report_shows_sold_product_transaction_details_and_exports_them(): void
    {
        $product = Product::query()->create([
            'name' => 'Whey Protein Vanilla',
            'category' => 'vitamin',
            'brand' => 'FitFuel',
            'sku' => 'WF-001',
            'price' => 125000,
            'stock' => 8,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        CashierTransaction::query()->create([
            'invoice' => 'PRD-DETAIL-001',
            'product_id' => $product->id,
            'customer_name' => 'Bima Santoso',
            'transaction_group' => 'product_sale',
            'transaction_type' => $product->name,
            'amount' => 250000,
            'quantity' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'verified',
            'receipt_status' => 'ready',
            'transaction_at' => now()->startOfMonth()->addDays(2)->setTime(13, 30),
        ]);

        $session = [
            'auth' => [
                'role' => 'admin',
                'login' => 'admin',
            ],
        ];

        $this->withSession($session)
            ->get(route('admin.reports.show', [
                'reportSlug' => 'laporan-stok-barang',
                'detail_month' => now()->format('Y-m'),
            ]))
            ->assertOk()
            ->assertSee('Rincian Barang Terjual')
            ->assertSee('PRD-DETAIL-001')
            ->assertSee('Bima Santoso')
            ->assertSee('Whey Protein Vanilla')
            ->assertSee('FitFuel')
            ->assertSee('WF-001')
            ->assertSee('2 pcs')
            ->assertSee('Rp125.000')
            ->assertSee('Rp250.000');

        $this->withSession($session)
            ->get(route('admin.reports.show', [
                'reportSlug' => 'laporan-stok-barang',
                'detail_month' => now()->format('Y-m'),
                'export' => 1,
            ]))
            ->assertOk()
            ->assertSee('Rincian Barang Terjual')
            ->assertSee('PRD-DETAIL-001')
            ->assertSee('Bima Santoso');
    }
}
