<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
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

    $activeMembers = GymMember::query()
        ->where('member_status', 'member')
        ->where(function ($q) use ($today) {
            $q->where('package_status', 'active')
                ->orWhereDate('expires_at', '>=', $today);
        })
        ->count();

    $memberCheckinsToday  = GymCheckin::query()->whereDate('checked_in_at', $today)->where('verification_status', 'verified')->count();
    $nonMemberVisitsToday = GymMember::query()->where('member_status', 'non_member')->whereDate('visit_date', $today)->count();

    $expiringMemberships = GymMember::query()
        ->where('member_status', 'member')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '>=', $today)
        ->whereDate('expires_at', '<=', $sevenDaysFromNow)
        ->count();

    $todayRevenue    = CashierTransaction::query()->where('payment_status', 'verified')->whereDate('transaction_at', $today)->sum('amount');
    $todayExpense    = ExpenseRecord::query()->whereDate('expense_date', $today)->sum('amount');
    $todayNetRevenue = $todayRevenue - $todayExpense;

    $readyReports = collect([
        GymMember::query()->where('member_status', 'member')->exists(),
        GymMember::query()->where('member_status', 'non_member')->exists(),
        GymCheckin::query()->where('verification_status', 'verified')->exists(),
    ])->filter()->count();

    $heroSummary = [
        [
            'label'    => 'Check-in Hari Ini',
            'value'    => $memberCheckinsToday + $nonMemberVisitsToday,
            'note'     => "{$memberCheckinsToday} member check-in, {$nonMemberVisitsToday} non member tercatat",
            'emphasis' => false,
        ],
        [
            'label'    => 'Membership Alert',
            'value'    => $expiringMemberships . ' Jatuh Tempo',
            'note'     => $expiringMemberships > 0
                ? 'Akan berakhir dalam 7 hari dan perlu follow-up'
                : 'Belum ada membership yang perlu follow-up',
            'emphasis' => true,
        ],
        [
            'label'    => 'Laporan Harian',
            'value'    => $readyReports . ' Laporan',
            'note'     => 'Data member, non member, dan check-in siap ditinjau',
            'emphasis' => false,
        ],
    ];

    $dashboardStats = [
        ['label' => 'Total Member Aktif',    'value' => number_format($activeMembers, 0, ',', '.'),                'icon' => 'users',        'note' => 'member dengan paket aktif saat ini'],
        ['label' => 'Pemasukan Hari Ini',    'value' => 'Rp ' . number_format($todayRevenue, 0, ',', '.'),         'icon' => 'trending-up',  'note' => 'total transaksi terverifikasi yang tercatat hari ini'],
        ['label' => 'Pengeluaran Hari Ini',  'value' => 'Rp ' . number_format($todayExpense, 0, ',', '.'),         'icon' => 'trending-down','note' => 'total pengeluaran operasional yang dicatat hari ini'],
        ['label' => 'Laba Bersih Hari Ini',  'value' => 'Rp ' . number_format($todayNetRevenue, 0, ',', '.'),      'icon' => 'pie-chart',    'note' => 'selisih pemasukan dan pengeluaran yang tercatat hari ini'],
    ];

    $recentMembers = GymMember::query()->orderByDesc('created_at')->take(3)->get();

    return view('admin.dashboard_home', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'stats'         => $dashboardStats,
        'heroSummary'   => $heroSummary,
        'recentMembers' => $recentMembers,
    ]));
})->name('dashboard');
