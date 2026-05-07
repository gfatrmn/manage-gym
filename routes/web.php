<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes dibagi per modul untuk menjaga kerapihan struktur kode.
|
| routes/
|   web.php                  ← Entry point (file ini)
|   auth.php                 ← Login & logout
|   member-checkin.php       ← Self-service check-in member via QR
|   admin/
|     dashboard.php          ← Dashboard admin
|     members.php            ← Manajemen member
|     non-members.php        ← Manajemen non-member / tamu
|     products.php           ← Manajemen produk
|     checkins.php           ← Check-in admin
|     announcements.php      ← Pengumuman
|     reports.php            ← Laporan & ekspor
|   cashier/
|     dashboard.php          ← Dashboard kasir
|     checkins.php           ← Check-in kasir & validasi pending
|     member-payments.php    ← Pembayaran membership
|     daily-payments.php     ← Daily pass / non-member
|     transactions.php       ← Semua transaksi & penjualan produk
|     receipts.php           ← Bukti pembayaran & verifikasi QRIS
|
*/

require __DIR__.'/auth.php';
require __DIR__.'/member-checkin.php';

Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin/dashboard.php';
    require __DIR__.'/admin/members.php';
    require __DIR__.'/admin/non-members.php';
    require __DIR__.'/admin/products.php';
    require __DIR__.'/admin/checkins.php';
    require __DIR__.'/admin/announcements.php';
    require __DIR__.'/admin/reports.php';
});

Route::prefix('cashier')->name('cashier.')->group(function () {
    require __DIR__.'/cashier/dashboard.php';
    require __DIR__.'/cashier/checkins.php';
    require __DIR__.'/cashier/member-payments.php';
    require __DIR__.'/cashier/daily-payments.php';
    require __DIR__.'/cashier/transactions.php';
    require __DIR__.'/cashier/receipts.php';
});
