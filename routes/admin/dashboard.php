<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $startOfToday = now()->startOfDay();
    $endOfToday   = now()->endOfDay();

    // 1. Statistik
    $activeMembersCount = GymMember::where('status', 'member')
        ->where('expires_at', '>=', now())
        ->count();

    $totalPemasukanHariIni = CashierTransaction::whereBetween('transaction_at', [$startOfToday, $endOfToday])
        ->where('payment_status', 'verified')
        ->sum('amount')
        + DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->sum('payment_amount');

    $totalPengeluaranHariIni = ExpenseRecord::whereBetween('expense_date', [$startOfToday, $endOfToday])
        ->sum('amount');

    $labaBersihHariIni = $totalPemasukanHariIni - $totalPengeluaranHariIni;

    // 2. Member Baru - 3 Data Terbaru
    $recentMembersQuery = GymMember::query();

    if (Schema::hasColumn('gym_members', 'member_status')) {
        $recentMembersQuery->where('member_status', 'member');
    } else {
        $recentMembersQuery->whereNotNull('expires_at');
    }

    $recentMembers = $recentMembersQuery
        ->latest()
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

    // 3. Member yang perlu pengingat perpanjangan
    $expiringMembersQuery = GymMember::query()
        ->where('status', 'member')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '>=', now())
        ->whereDate('expires_at', '<=', now()->addDays(7));

    if (Schema::hasColumn('gym_members', 'member_status')) {
        $expiringMembersQuery->where('member_status', 'member');
    }

    $expiringMembers = $expiringMembersQuery
        ->orderBy('expires_at')
        ->orderBy('full_name')
        ->get()
        ->filter(function ($member) {
            $daysLeft = (int) ceil(now()->diffInDays($member->expires_at, false));
            $lastReminder = $member->last_membership_reminder_at;

            // Jika sudah pernah diingatkan pada rentang H-7 s/d H-4,
            // sembunyikan dulu dari list sampai masuk H-3.
            if ($daysLeft > 3 && $lastReminder) {
                return false;
            }

            return true;
        })
        ->take(3)
        ->map(function ($member) {
            $daysLeft = (int) ceil(now()->diffInDays($member->expires_at, false));

            return (object) [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'phone' => $member->phone ?: '-',
                'expires_at' => $member->expires_at->translatedFormat('d M Y'),
                'days_left' => $daysLeft,
                'last_reminder' => $member->last_membership_reminder_at
                    ? $member->last_membership_reminder_at->translatedFormat('d M Y, H:i')
                    : 'Belum',
            ];
        });

    // 4. Alert & Meta
    $expiringCount = GymMember::where('status', 'member')
        ->whereBetween('expires_at', [now(), now()->addDays(7)])
        ->count();

    // 5. Data untuk modal Aksi Cepat
    $memberOptions   = GymMember::where('status', 'member')
        ->where('expires_at', '>=', now())
        ->orderBy('full_name')
        ->get(['id', 'full_name', 'checkin_code']);

    $paymentMethods  = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];

    return view('admin.dashboard_home', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'stats' => [
            [
                'label' => 'Total Member Aktif',
                'value' => number_format($activeMembersCount, 0, ',', '.'),
                'note'  => 'Status member',
            ],
            [
                'label' => 'Pemasukan Hari Ini',
                'value' => 'Rp' . number_format($totalPemasukanHariIni, 0, ',', '.'),
                'note'  => 'Kasir & Daily Pass',
            ],
            [
                'label' => 'Pengeluaran Hari Ini',
                'value' => 'Rp' . number_format($totalPengeluaranHariIni, 0, ',', '.'),
                'note'  => 'Biaya operasional',
            ],
            [
                'label' => 'Laba Bersih Hari Ini',
                'value' => 'Rp' . number_format($labaBersihHariIni, 0, ',', '.'),
                'note'  => 'Pemasukan - pengeluaran',
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
        'expiringMembers'=> $expiringMembers,
        'memberOptions'  => $memberOptions,
        'paymentMethods' => $paymentMethods,
    ]));
})->name('dashboard');
