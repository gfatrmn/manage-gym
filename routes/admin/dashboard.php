<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
use App\Models\CashierTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $startOfToday = now()->startOfDay();
    $endOfToday = now()->endOfDay();

    // 1. Hitung Member Aktif
    $activeMembersCount = GymMember::where('status', 'active')
        ->where('expires_at', '>=', now())
        ->count();

    // 2. HITUNG PEMASUKAN HARI INI (Dua Sumber)
    // Sumber A: Dari tabel Transaksi Kasir (Membership/Produk)
    $incomeFromTransactions = CashierTransaction::whereBetween('transaction_at', [$startOfToday, $endOfToday])
        ->where('payment_status', 'verified')
        ->sum('amount');

    // Sumber B: Dari tabel DailyGuest (Tamu Harian)
    // Kita ambil kolom 'payment_amount' dari pendaftaran guest hari ini
    $incomeFromGuests = DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->sum('payment_amount');

    $totalPemasukanHariIni = $incomeFromTransactions + $incomeFromGuests;

    // 3. AMBIL LOG AKTIVITAS (Member + Guest)
    $memberLogs = GymCheckin::with('member')
        ->whereBetween('checked_in_at', [$startOfToday, $endOfToday])
        ->get()
        ->map(fn($item) => [
            'nama'  => $item->member->full_name ?? 'N/A',
            'tipe'  => 'Member',
            'waktu' => $item->checked_in_at->format('H:i')
        ]);

    $guestLogs = DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->get()
        ->map(fn($item) => [
            'nama'  => $item->full_name,
            'tipe'  => 'Guest',
            'waktu' => $item->created_at->format('H:i')
        ]);

    $allLogs = $memberLogs->concat($guestLogs);
    $totalCheckins = $allLogs->count();
    $recentCheckins = $allLogs->sortByDesc('waktu')->take(3);

    // 4. Membership Alert
    $expiringCount = GymMember::where('status', 'active')
        ->whereBetween('expires_at', [now(), now()->addDays(7)])
        ->count();

    return view('admin.dashboard_home', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'stats' => [
            [
                'label' => 'Total Member Aktif',
                'value' => number_format($activeMembersCount, 0, ',', '.'),
                'note'  => 'Status aktif'
            ],
            [
                'label' => 'Pemasukan Hari Ini',
                'value' => 'Rp ' . number_format($totalPemasukanHariIni, 0, ',', '.'),
                'note'  => 'Kasir: Rp'.number_format($incomeFromTransactions,0,',','.').' | Guest: Rp'.number_format($incomeFromGuests,0,',','.')
            ],
        ],
        'heroSummary' => [
            [
                'label' => 'Check-in Hari Ini',
                'value' => $totalCheckins,
                'note'  => $memberLogs->count() . ' Member & ' . $guestLogs->count() . ' Guest'
            ],
            [
                'label' => 'Membership Alert',
                'value' => $expiringCount . ' Jatuh Tempo',
                'note'  => 'Akan berakhir dalam 7 hari'
            ]
        ],
        'recentMembers'  => GymMember::latest()->take(3)->get(),
        'recentCheckins' => $recentCheckins,
    ]));
})->name('dashboard');
