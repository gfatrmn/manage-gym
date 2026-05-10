<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest; // Tambahkan model ini
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today            = Carbon::today();
    $sevenDaysFromNow = $today->copy()->addDays(7);

    // 1. Perbaikan: Menggunakan kolom 'status' (bukan member_status)
    $activeMembers = GymMember::query()
        ->where('status', 'active')
        ->whereDate('expires_at', '>=', $today)
        ->count();

    // 2. Perbaikan: Ambil data Check-in Member
    $memberCheckinsToday = GymCheckin::query()
        ->whereDate('checked_in_at', $today)
        ->count();

    // 3. Perbaikan: Ambil data Non-Member dari tabel daily_guests (bukan gym_members)
    // Perbaikan query untuk Non-Member/Tamu
    $nonMemberVisitsToday = \App\Models\DailyGuest::query()
        ->whereDate('visit_at', \Illuminate\Support\Carbon::today())
        ->count();

    // 4. Perbaikan: Cek masa aktif member
    $expiringMemberships = GymMember::query()
        ->where('status', 'active')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '>=', $today)
        ->whereDate('expires_at', '<=', $sevenDaysFromNow)
        ->count();

    $todayRevenue = CashierTransaction::query()
        ->where('payment_status', 'verified')
        ->whereDate('transaction_at', $today)
        ->sum('amount');

    $todayExpense = ExpenseRecord::query()
        ->whereDate('expense_date', $today)
        ->sum('amount');

    $todayNetRevenue = $todayRevenue - $todayExpense;

    $heroSummary = [
        [
            'label'    => 'Check-in Hari Ini',
            'value'    => $memberCheckinsToday + $nonMemberVisitsToday,
            'note'     => "{$memberCheckinsToday} member check-in, {$nonMemberVisitsToday} tamu harian",
            'emphasis' => false,
        ],
        [
            'label'    => 'Membership Alert',
            'value'    => $expiringMemberships . ' Jatuh Tempo',
            'note'     => $expiringMemberships > 0
                ? 'Akan berakhir dalam 7 hari'
                : 'Belum ada masa aktif yang hampir habis',
            'emphasis' => true,
        ]
    ];

    $dashboardStats = [
        ['label' => 'Total Member Aktif', 'value' => number_format($activeMembers, 0, ',', '.'), 'icon' => 'users'],
        ['label' => 'Pemasukan Hari Ini', 'value' => 'Rp ' . number_format($todayRevenue, 0, ',', '.'), 'icon' => 'trending-up'],
    ];

    // Mengambil 3 member yang baru bergabung
    // Benar: Cukup ambil data terbaru karena tabel ini sudah khusus member
    $recentMembers = GymMember::query()
        ->orderByDesc('created_at')
        ->take(3)
        ->get();

    return view('admin.dashboard_home', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'stats'         => $dashboardStats,
        'heroSummary'   => $heroSummary,
        'recentMembers' => $recentMembers,
    ]));
})->name('dashboard');
