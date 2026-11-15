<?php

namespace App\Http\Controllers\Cashier;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();

        // 1. 5 Transaksi POS Terbaru
        $latestTransactions = CashierTransaction::query()
            ->with(['member', 'product'])
            ->latest('transaction_at')
            ->limit(5)
            ->get()
            ->map(fn (CashierTransaction $t) => [
                'id' => $t->id,
                'invoice' => $t->invoice,
                'customer_name' => $t->customer_name ?? $t->member?->full_name ?? 'Pelanggan',
                'transaction_type' => $t->transaction_type ?: ($t->transaction_group === 'product' ? 'Penjualan Produk' : 'Transaksi POS'),
                'payment_method' => strtoupper((string) ($t->payment_method ?? 'CASH')),
                'amount' => (int) $t->amount,
                'amount_formatted' => RouteHelpers::formatCurrency($t->amount),
                'payment_status' => $t->payment_status,
                'time' => $t->transaction_at?->format('H:i') ?? '-',
                'date_formatted' => $t->transaction_at?->translatedFormat('d M, H:i') ?? '-',
            ]);

        // 2. 5 Riwayat Check-in Member & Non Member Terbaru
        $memberCheckins = GymCheckin::query()
            ->with('member')
            ->latest('checked_in_at')
            ->limit(5)
            ->get()
            ->map(fn (GymCheckin $item) => [
                'id' => 'm-' . $item->id,
                'name' => $item->member?->full_name ?? $item->submitted_name ?? 'Member',
                'category' => 'Member',
                'is_member' => true,
                'code' => $item->member?->checkin_code ?? '-',
                'time' => $item->checked_in_at?->format('H:i') ?? '-',
                'date_formatted' => $item->checked_in_at?->translatedFormat('d M, H:i') ?? '-',
                'timestamp' => $item->checked_in_at?->timestamp ?? 0,
                'status' => $item->verification_status === 'verified' ? 'Hadir' : 'Pending',
            ]);

        $nonMemberCheckins = CashierTransaction::query()
            ->where('transaction_group', 'daily_pass')
            ->latest('transaction_at')
            ->limit(5)
            ->get()
            ->map(fn (CashierTransaction $item) => [
                'id' => 'nm-' . $item->id,
                'name' => $item->customer_name ?: 'Tamu Harian',
                'category' => 'Daily Pass',
                'is_member' => false,
                'code' => $item->invoice,
                'time' => $item->transaction_at?->format('H:i') ?? '-',
                'date_formatted' => $item->transaction_at?->translatedFormat('d M, H:i') ?? '-',
                'timestamp' => $item->transaction_at?->timestamp ?? 0,
                'status' => $item->payment_status === 'verified' ? 'Hadir' : 'Pending',
            ]);

        $latestCheckins = $memberCheckins->concat($nonMemberCheckins)
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();

        // Count stats for the 4 compact KPI cards
        $todayMemberCheckinCount = GymCheckin::query()
            ->whereDate('checked_in_at', $today)
            ->where('verification_status', 'verified')
            ->count();

        $todayDailyPassCount = CashierTransaction::query()
            ->whereDate('transaction_at', $today)
            ->where('transaction_group', 'daily_pass')
            ->where('payment_status', 'verified')
            ->count();

        return Inertia::render('Cashier/Dashboard', RouteHelpers::buildCashierViewData([
            'latestTransactions' => $latestTransactions,
            'latestCheckins' => $latestCheckins,
            'todayMemberCheckinCount' => $todayMemberCheckinCount,
            'todayDailyPassCount' => $todayDailyPassCount,
        ]));
    }
}

