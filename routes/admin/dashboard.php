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
    $endOfToday   = now()->endOfDay();

    // 1. Statistik
    $activeMembersCount = GymMember::where('status', 'active')
        ->where('expires_at', '>=', now())
        ->count();

    $totalPemasukanHariIni = CashierTransaction::whereBetween('transaction_at', [$startOfToday, $endOfToday])
        ->where('payment_status', 'verified')
        ->sum('amount')
        + DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->sum('payment_amount');

    // 2. Member Baru - 3 Data Terbaru
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

    // 3. Log Aktivitas - 3 Aktivitas Terbaru
    $memberLogs = GymCheckin::with('member')
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->take(3)
        ->get()
        ->map(fn($item) => [
            'nama'      => $item->member->full_name ?? 'N/A',
            'tipe'      => 'Member',
            'waktu_raw' => $item->checked_in_at,
            'waktu'     => $item->checked_in_at->translatedFormat('d M Y, H:i'),
        ]);

    $guestLogs = DailyGuest::latest()
        ->take(3)
        ->get()
        ->map(fn($item) => [
            'nama'      => $item->full_name,
            'tipe'      => 'Guest',
            'waktu_raw' => $item->created_at,
            'waktu'     => $item->created_at->translatedFormat('d M Y, H:i'),
        ]);

    $recentCheckins = $memberLogs->concat($guestLogs)
        ->sortByDesc('waktu_raw')
        ->take(3)
        ->values();

    // 4. Alert & Meta
    $expiringCount = GymMember::where('status', 'active')
        ->whereBetween('expires_at', [now(), now()->addDays(7)])
        ->count();

    // 5. Data untuk modal Aksi Cepat
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
                'note'  => 'Kasir & Guest',
            ],
        ],
        'heroSummary' => [
            [
                'label' => 'Aktivitas Hari Ini',
                'value' => GymCheckin::whereBetween('checked_in_at', [$startOfToday, $endOfToday])->count()
                         + DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])->count(),
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
        'memberOptions'  => $memberOptions,   // untuk modal check-in
        'paymentMethods' => $paymentMethods,  // untuk modal guest & tambah member
    ]));
})->name('dashboard');
