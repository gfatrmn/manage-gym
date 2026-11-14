<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\DailyGuest;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();
        $startOfToday = now()->startOfDay();
        $endOfToday = now()->endOfDay();

        $activeMembersCount = GymMember::query()
            ->where('status', 'member')
            ->whereDate('expires_at', '>=', $today)
            ->count();

        $totalPemasukanHariIni = (int) CashierTransaction::query()
            ->whereBetween('transaction_at', [$startOfToday, $endOfToday])
            ->where('payment_status', 'verified')
            ->sum('amount')
            + (int) DailyGuest::query()
                ->whereBetween('created_at', [$startOfToday, $endOfToday])
                ->sum('payment_amount');

        $totalPengeluaranHariIni = (int) ExpenseRecord::query()
            ->whereDate('expense_date', $today)
            ->sum('amount');

        $labaBersihHariIni = $totalPemasukanHariIni - $totalPengeluaranHariIni;

        $recentMembersQuery = GymMember::query()->where('status', 'member');
        if (Schema::hasColumn('gym_members', 'member_status')) {
            $recentMembersQuery->where('member_status', 'member');
        }

        $recentMembers = $recentMembersQuery
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (GymMember $member) => [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'phone' => $member->phone ?: '-',
                'checkin_code' => $member->checkin_code,
                'created_at' => $member->created_at?->translatedFormat('d M Y') ?? '-',
                'expires_at' => $member->expires_at?->translatedFormat('d M Y') ?? '-',
            ])
            ->values();

        $expiringMembersQuery = GymMember::query()
            ->where('status', 'member')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', $today)
            ->whereDate('expires_at', '<=', $today->copy()->addDays(7));

        if (Schema::hasColumn('gym_members', 'member_status')) {
            $expiringMembersQuery->where('member_status', 'member');
        }

        $expiringMembers = $expiringMembersQuery
            ->orderBy('expires_at')
            ->orderBy('full_name')
            ->get()
            ->filter(function (GymMember $member) use ($today) {
                $daysLeft = (int) $today->diffInDays($member->expires_at->copy()->startOfDay(), false);
                if ($daysLeft > 3 && $member->last_membership_reminder_at) {
                    return false;
                }

                return true;
            })
            ->take(5)
            ->map(function (GymMember $member) use ($today) {
                $daysLeft = (int) $today->diffInDays($member->expires_at->copy()->startOfDay(), false);

                return [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'phone' => $member->phone ?: '-',
                    'expires_at' => $member->expires_at->translatedFormat('d M Y'),
                    'days_left' => $daysLeft,
                    'last_reminder' => $member->last_membership_reminder_at
                        ? $member->last_membership_reminder_at->translatedFormat('d M Y, H:i')
                        : 'Belum',
                ];
            })
            ->values();

        $expiringCount = GymMember::query()
            ->where('status', 'member')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', $today)
            ->whereDate('expires_at', '<=', $today->copy()->addDays(7))
            ->count();

        $todayVisits = GymCheckin::query()
            ->where('verification_status', 'verified')
            ->whereBetween('checked_in_at', [$startOfToday, $endOfToday])
            ->count()
            + DailyGuest::query()->whereBetween('created_at', [$startOfToday, $endOfToday])->count();

        $memberOptions = GymMember::query()
            ->where('status', 'member')
            ->whereDate('expires_at', '>=', $today)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'checkin_code']);

        return Inertia::render('Admin/Dashboard', [
            'pageTitle' => 'Dashboard Admin Arena Gym',
            'activePage' => 'dashboard',
            'stats' => [
                [
                    'label' => 'Total Member Aktif',
                    'value' => number_format($activeMembersCount, 0, ',', '.'),
                    'note' => 'Status member aktif',
                ],
                [
                    'label' => 'Pemasukan Hari Ini',
                    'value' => RouteHelpers::formatCurrency($totalPemasukanHariIni),
                    'note' => 'Kasir & Daily Pass',
                ],
                [
                    'label' => 'Pengeluaran Hari Ini',
                    'value' => RouteHelpers::formatCurrency($totalPengeluaranHariIni),
                    'note' => 'Biaya operasional',
                ],
                [
                    'label' => 'Laba Bersih Hari Ini',
                    'value' => RouteHelpers::formatCurrency($labaBersihHariIni),
                    'note' => 'Pemasukan - pengeluaran',
                ],
            ],
            'heroSummary' => [
                [
                    'label' => 'Aktivitas Hari Ini',
                    'value' => $todayVisits . ' Kunjungan',
                    'note' => 'Check-in & Daily Pass',
                ],
                [
                    'label' => 'Membership Alert',
                    'value' => $expiringCount . ' Jatuh Tempo',
                    'note' => 'Dalam 7 hari ke depan',
                ],
            ],
            'recentMembers' => $recentMembers,
            'expiringMembers' => $expiringMembers,
            'memberOptions' => $memberOptions,
            'paymentMethods' => ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'],
        ]);
    }
}
