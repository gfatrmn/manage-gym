<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
use App\Models\CashierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $startOfToday = now()->startOfDay();
    $endOfToday   = now()->endOfDay();

    // 1. Statistik - Member Aktif
    $activeMembersCount = GymMember::where('status', 'active')
        ->where('expires_at', '>=', now())
        ->count();

    // 2. Statistik - Pemasukan Hari Ini
    // DIUBAH: Menggunakan where(function...) untuk mengecek ke transaction_at ATAU created_at
    // dan memastikan mengambil status 'verified' (jika cash) atau disesuaikan dengan logic aplikasi Anda
    $pemasukanKasir = CashierTransaction::where(function($query) use ($startOfToday, $endOfToday) {
            $query->whereBetween('transaction_at', [$startOfToday, $endOfToday])
                  ->orWhereBetween('created_at', [$startOfToday, $endOfToday]);
        })
        ->whereIn('payment_status', ['verified', 'success']) // Antisipasi jika statusnya bernama 'success' atau 'verified'
        ->where('transaction_group', '!=', 'daily_pass')    // Tetap abaikan daily pass agar tidak double-count dengan tabel guest
        ->sum('amount');

    // Mengambil transaksi harian murni dari tabel daily_guests Anda
    $pemasukanGuest = DailyGuest::whereBetween('visit_at', [$startOfToday, $endOfToday])
        ->sum('payment_amount');

    // Total gabungan pendapatan hari ini
    $totalPemasukanHariIni = $pemasukanKasir + $pemasukanGuest;


    // 3. Member Baru - 3 Data Terbaru
    $recentMembers = GymMember::latest()
        ->take(3)
        ->get()
        ->map(function ($member) {
            return (object) [
                'full_name'  => $member->full_name,
                'phone'      => $member->phone ?? '-',
                'created_at' => $member->created_at->translatedFormat('d M Y'),
                'expires_at' => $member->expires_at
                    ? $member->expires_at->translatedFormat('d M Y')
                    : '-',
            ];
        });

    // 4. Log Aktivitas - 3 Aktivitas Terbaru (Member Check-in)
    $memberLogs = GymCheckin::with('member')
        ->where('verification_status', 'verified')
        ->whereNotNull('checked_in_at')
        ->latest('checked_in_at')
        ->take(3)
        ->get()
        ->map(fn($item) => [
            'nama'      => $item->member->full_name ?? 'N/A',
            'tipe'      => 'Member',
            'waktu_raw' => $item->checked_in_at,
            'waktu'     => $item->checked_in_at->translatedFormat('d M Y, H:i'),
        ]);

    // 5. Log Aktivitas - 3 Aktivitas Terbaru (Daily Guest Visit)
    $guestLogs = DailyGuest::latest('visit_at')
        ->take(3)
        ->get()
        ->map(fn($item) => [
            'nama'      => $item->full_name,
            'tipe'      => 'Guest',
            'waktu_raw' => $item->visit_at ?? $item->created_at,
            'waktu'     => Carbon::parse($item->visit_at ?? $item->created_at)->translatedFormat('d M Y, H:i'),
        ]);

    // Menggabungkan logs check-in member dan kunjungan harian tamu
    $recentCheckins = $memberLogs->concat($guestLogs)
        ->sortByDesc('waktu_raw')
        ->take(3)
        ->values();

    // 6. Alert & Meta
    $expiringCount = GymMember::where('status', 'active')
        ->whereBetween('expires_at', [now(), now()->addDays(7)])
        ->count();

    // 7. Data untuk modal Aksi Cepat
    $memberOptions   = GymMember::where('status', 'active')
        ->where('expires_at', '>=', now())
        ->orderBy('full_name')
        ->get(['id', 'full_name', 'checkin_code']);

    $paymentMethods  = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];

    return view('admin.dashboard_home', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'stats' => [
            [
                'label' => 'Total Member Aktif',
                'value' => number_format($activeMembersCount, 0, ',', '.'),
                'note'  => 'Status aktif',
            ],
            [
                'label' => 'Pemasukan Hari Ini',
                'value' => 'Rp ' . number_format($totalPemasukanHariIni, 0, ',', '.'),
                'note'  => 'Daily, Member & Perpanjang',
            ],
        ],
        'heroSummary' => [
            [
                'label' => 'Aktivitas Hari Ini',
                'value' => GymCheckin::whereBetween('checked_in_at', [$startOfToday, $endOfToday])->count()
                         + DailyGuest::whereBetween('visit_at', [$startOfToday, $endOfToday])->count(),
                'note'  => 'Kunjungan',
            ],
            [
                'label' => 'Membership Alert',
                'value' => $expiringCount . ' Jatuh Tempo',
                'note'  => 'Dalam 7 hari',
            ],
        ],
        'recentMembers'  => $recentMembers,
        'recentCheckins' => $recentCheckins,
        'memberOptions'  => $memberOptions,
        'paymentMethods' => $paymentMethods,
    ]));
})->name('dashboard');
