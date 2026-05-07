<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Admin – Laporan & Ekspor
|--------------------------------------------------------------------------
*/

// ─────────────────────────────────────────────────────────────────────────────
// Helper: build semua data yang dibutuhkan halaman laporan
// ─────────────────────────────────────────────────────────────────────────────
$buildAdminReportsData = function (Request $request): array {
    $memberRecords      = GymMember::query()->where('member_status', 'member')->get();
    $nonMemberRecords   = GymMember::query()->where('member_status', 'non_member')->get();
    $checkinRecords     = GymCheckin::query()->with('member')->where('verification_status', 'verified')->latest('checked_in_at')->get();
    $cashierTransactions= CashierTransaction::query()->with(['member', 'product'])->latest('transaction_at')->get();
    $expenseRecords     = ExpenseRecord::query()->latest('expense_date')->latest()->get();

    $verifiedTransactions    = $cashierTransactions->filter(fn (CashierTransaction $t) => $t->payment_status === 'verified');
    $memberPaymentRecords     = $cashierTransactions->where('transaction_group', 'member_payment')->values();
    $nonMemberPaymentRecords  = $cashierTransactions->where('transaction_group', 'daily_pass')->values();
    $productSaleRecords       = $cashierTransactions->where('transaction_group', 'product_sale')->values();
    $otherTransactionRecords  = $cashierTransactions->where('transaction_group', 'other')->values();

    $activeMembers     = $memberRecords->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->gt(now()->addDays(7)));
    $endingSoonMembers = $memberRecords->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->lte(now()->addDays(7)) && $m->expires_at->gte(now()));
    $expiredMembers    = $memberRecords->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->lt(now()));

    $membershipReportSummary = [
        'active'  => $activeMembers->count(),
        'ending'  => $endingSoonMembers->count(),
        'expired' => $expiredMembers->count(),
    ];

    // ── Filter params ─────────────────────────────────────────────────────────
    $activeDetailTab = in_array($request->query('detail_tab'), [
        'activity', 'membership', 'member-payments', 'non-member-payments', 'other-transactions', 'expenses',
    ], true) ? $request->query('detail_tab') : 'activity';

    $detailFilterType = in_array($request->query('detail_filter'), ['month', 'range'], true)
        ? $request->query('detail_filter')
        : 'month';

    $detailMonthInput = (string) $request->query('detail_month', now()->format('Y-m'));
    $detailMonth      = preg_match('/^\d{4}-\d{2}$/', $detailMonthInput)
        ? Carbon::createFromFormat('Y-m', $detailMonthInput)->startOfMonth()
        : now()->startOfMonth();

    $detailFrom = $request->filled('detail_from')
        ? Carbon::parse($request->query('detail_from'))->startOfDay()
        : $detailMonth->copy()->startOfMonth();

    $detailTo = $request->filled('detail_to')
        ? Carbon::parse($request->query('detail_to'))->endOfDay()
        : $detailMonth->copy()->endOfMonth();

    if ($detailTo->lt($detailFrom)) {
        [$detailFrom, $detailTo] = [$detailTo->copy()->startOfDay(), $detailFrom->copy()->endOfDay()];
    }

    $detailRangeStart = $detailFilterType === 'range' ? $detailFrom->copy()->startOfDay() : $detailMonth->copy()->startOfMonth();
    $detailRangeEnd   = $detailFilterType === 'range' ? $detailTo->copy()->endOfDay()     : $detailMonth->copy()->endOfMonth();
    $detailYearStart  = $detailMonth->copy()->startOfYear();
    $detailYearEnd    = $detailMonth->copy()->endOfYear();

    $isDateIncluded = function ($date) use ($detailFilterType, $detailMonth, $detailFrom, $detailTo): bool {
        if (! $date) {
            return false;
        }
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $detailFilterType === 'range'
            ? $date->betweenIncluded($detailFrom, $detailTo)
            : $date->betweenIncluded($detailMonth->copy()->startOfMonth(), $detailMonth->copy()->endOfMonth());
    };

    // ── Financial summary (bulan aktif) ───────────────────────────────────────
    $reportMonthStart = $detailMonth->copy()->startOfMonth();
    $reportMonthEnd   = $detailMonth->copy()->endOfMonth();

    $monthlyVerifiedTransactions = $verifiedTransactions
        ->filter(fn (CashierTransaction $t) => $t->transaction_at->between($reportMonthStart, $reportMonthEnd))
        ->values();
    $monthlyExpenseRecords = $expenseRecords
        ->filter(fn (ExpenseRecord $e) => $e->expense_date->between($reportMonthStart, $reportMonthEnd))
        ->values();

    $memberRevenue    = $monthlyVerifiedTransactions->where('transaction_group', 'member_payment')->sum('amount');
    $nonMemberRevenue = $monthlyVerifiedTransactions->where('transaction_group', 'daily_pass')->sum('amount');
    $productRevenue   = $monthlyVerifiedTransactions->where('transaction_group', 'product_sale')->sum('amount');
    $otherRevenue     = $monthlyVerifiedTransactions->where('transaction_group', 'other')->sum('amount');
    $totalExpense     = $monthlyExpenseRecords->sum('amount');
    $totalRevenue     = $memberRevenue + $nonMemberRevenue + $productRevenue + $otherRevenue;

    $financialSummary = [
        'month_label'              => $reportMonthStart->translatedFormat('F Y'),
        'member_revenue'           => $memberRevenue,
        'non_member_revenue'       => $nonMemberRevenue,
        'product_revenue'          => $productRevenue,
        'other_revenue'            => $otherRevenue,
        'total_revenue'            => $totalRevenue,
        'total_expense'            => $totalExpense,
        'net_revenue'              => $totalRevenue - $totalExpense,
        'verified_transaction_count'=> $monthlyVerifiedTransactions->count(),
        'expense_count'            => $monthlyExpenseRecords->count(),
    ];

    // ── Filtered records (untuk tabel detail) ────────────────────────────────
    $filteredTrainingActivities  = $checkinRecords->filter(fn (GymCheckin $a) => $isDateIncluded($a->checked_in_at))->take(20)->values();
    $filteredMemberPayments      = $memberPaymentRecords->filter(fn (CashierTransaction $t) => $isDateIncluded($t->transaction_at))->take(20)->values();
    $filteredNonMemberPayments   = $nonMemberPaymentRecords->filter(fn (CashierTransaction $t) => $isDateIncluded($t->transaction_at))->take(20)->values();
    $filteredProductSales        = $productSaleRecords->filter(fn (CashierTransaction $t) => $isDateIncluded($t->transaction_at))->take(20)->values();
    $filteredOtherTransactions   = $otherTransactionRecords->filter(fn (CashierTransaction $t) => $isDateIncluded($t->transaction_at))->take(20)->values();
    $filteredExpenseRecords      = $expenseRecords->filter(fn (ExpenseRecord $e) => $isDateIncluded($e->expense_date))->take(20)->values();

    // ── Membership operational rows ───────────────────────────────────────────
    $membershipOperationalRows = $memberRecords
        ->sortBy('expires_at')
        ->map(function (GymMember $member) {
            $status = 'Aktif';
            $color  = 'success';
            if ($member->expires_at?->lt(now()))                   { $status = 'Sudah Habis';  $color = 'danger'; }
            elseif ($member->expires_at?->lte(now()->addDays(7)))  { $status = 'Akan Berakhir'; $color = 'warning'; }

            return [
                'name'             => $member->full_name,
                'profile_photo_url'=> $member->profile_photo_url,
                'profile_initials' => $member->profile_initials,
                'payment_method'   => strtoupper($member->payment_method ?? '-'),
                'joined_at'        => $member->joined_at,
                'expires_at'       => $member->expires_at,
                'status'           => $status,
                'color'            => $color,
            ];
        })
        ->values();

    $filteredMembershipRows = $membershipOperationalRows
        ->filter(fn (array $row) => $isDateIncluded($row['expires_at'] ?? $row['joined_at']))
        ->take(20)
        ->values();

    // ── Financial breakdowns ──────────────────────────────────────────────────
    $financialRevenueBreakdown = collect([
        ['label' => 'Pembayaran Member',     'description' => 'Transaksi membership, perpanjangan, dan pembayaran paket member yang sudah terverifikasi.',  'amount' => $memberRevenue,    'count' => $monthlyVerifiedTransactions->where('transaction_group', 'member_payment')->count()],
        ['label' => 'Pembayaran Non-Member', 'description' => 'Pemasukan daily pass atau kunjungan harian non-member yang sudah terverifikasi.',             'amount' => $nonMemberRevenue, 'count' => $monthlyVerifiedTransactions->where('transaction_group', 'daily_pass')->count()],
        ['label' => 'Penjualan Produk',      'description' => 'Pemasukan dari penjualan produk yang dilakukan oleh kasir.',                                  'amount' => $productRevenue,   'count' => $monthlyVerifiedTransactions->where('transaction_group', 'product_sale')->count()],
        ['label' => 'Transaksi Lain',        'description' => 'Pemasukan lain di luar membership, daily pass, dan penjualan produk.',                        'amount' => $otherRevenue,     'count' => $monthlyVerifiedTransactions->where('transaction_group', 'other')->count()],
    ]);

    $financialExpenseBreakdown = $monthlyExpenseRecords
        ->groupBy(fn (ExpenseRecord $e) => $e->category ?: 'lainnya')
        ->map(fn ($group, $category) => [
            'category' => Str::of((string) $category)->replace('_', ' ')->title()->value(),
            'amount'   => $group->sum('amount'),
            'count'    => $group->count(),
        ])
        ->sortByDesc('amount')
        ->values();

    $financialPaymentMethodBreakdown = $monthlyVerifiedTransactions
        ->groupBy(fn (CashierTransaction $t) => strtolower($t->payment_method ?: 'lainnya'))
        ->map(fn ($group, $method) => [
            'method' => strtoupper((string) $method),
            'amount' => $group->sum('amount'),
            'count'  => $group->count(),
        ])
        ->sortByDesc('amount')
        ->values();

    $financialDailyRevenue = $monthlyVerifiedTransactions
        ->groupBy(fn (CashierTransaction $t) => $t->transaction_at->format('Y-m-d'))
        ->map(fn ($group, $dateKey) => [
            'date'             => Carbon::createFromFormat('Y-m-d', $dateKey),
            'member_revenue'   => $group->where('transaction_group', 'member_payment')->sum('amount'),
            'non_member_revenue'=> $group->where('transaction_group', 'daily_pass')->sum('amount'),
            'other_revenue'    => $group->where('transaction_group', 'other')->sum('amount'),
            'total_revenue'    => $group->sum('amount'),
            'transaction_count'=> $group->count(),
        ])
        ->sortBy('date')
        ->values();

    $financialMonthlyHistory = collect(range(0, 5))
        ->map(function (int $offset) use ($verifiedTransactions, $expenseRecords) {
            $monthStart = now()->copy()->subMonths($offset)->startOfMonth();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            $mTx  = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->between($monthStart, $monthEnd));
            $mExp = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->between($monthStart, $monthEnd));

            $mMemberRev    = $mTx->where('transaction_group', 'member_payment')->sum('amount');
            $mNonMemberRev = $mTx->where('transaction_group', 'daily_pass')->sum('amount');
            $mOtherRev     = $mTx->where('transaction_group', 'other')->sum('amount');
            $mTotalRev     = $mMemberRev + $mNonMemberRev + $mOtherRev;
            $mTotalExp     = $mExp->sum('amount');

            return [
                'month_label'       => $monthStart->translatedFormat('F Y'),
                'member_revenue'    => $mMemberRev,
                'non_member_revenue'=> $mNonMemberRev,
                'other_revenue'     => $mOtherRev,
                'total_revenue'     => $mTotalRev,
                'total_expense'     => $mTotalExp,
                'net_revenue'       => $mTotalRev - $mTotalExp,
                'transaction_count' => $mTx->count(),
                'expense_count'     => $mExp->count(),
            ];
        })
        ->values()->reverse()->values();

    // ── Visitor daily rows ────────────────────────────────────────────────────
    $memberVisitorsByDay = $checkinRecords
        ->filter(fn (GymCheckin $a) => $a->checked_in_at->betweenIncluded($detailRangeStart, $detailRangeEnd))
        ->groupBy(fn (GymCheckin $a) => $a->checked_in_at->format('Y-m-d'))
        ->map(fn ($g) => $g->count());

    $nonMemberVisitorsByDay = $nonMemberRecords
        ->filter(fn (GymMember $m) => $m->visit_date && Carbon::parse($m->visit_date)->betweenIncluded($detailRangeStart, $detailRangeEnd))
        ->groupBy(fn (GymMember $m) => $m->visit_date->format('Y-m-d'))
        ->map(fn ($g) => $g->count());

    $visitorDailyRows = collect();
    for ($date = $detailRangeStart->copy(); $date->lte($detailRangeEnd); $date->addDay()) {
        $dk = $date->format('Y-m-d');
        $mc = (int) ($memberVisitorsByDay->get($dk) ?? 0);
        $nc = (int) ($nonMemberVisitorsByDay->get($dk) ?? 0);
        $visitorDailyRows->push(['date' => $date->copy(), 'member_count' => $mc, 'non_member_count' => $nc, 'total_count' => $mc + $nc]);
    }

    // ── Helpers untuk daily/monthly stat rows ──────────────────────────────────
    $buildDailyStatRows = function (callable $resolver) use ($detailMonth): array {
        $rows = [];
        for ($date = $detailMonth->copy()->startOfMonth(); $date->lte($detailMonth->copy()->endOfMonth()); $date->addDay()) {
            $rows[] = $resolver($date->copy());
        }
        return $rows;
    };

    $buildMonthlyStatRows = function (callable $resolver) use ($detailYearStart, $detailYearEnd): array {
        $rows = [];
        for ($month = $detailYearStart->copy(); $month->lte($detailYearEnd); $month->addMonth()) {
            $rows[] = $resolver($month->copy());
        }
        return $rows;
    };

    // ── Training stats ────────────────────────────────────────────────────────
    $dailyTrainingStats = [
        'total_checkins'      => $checkinRecords->count(),
        'today_checkins'      => $checkinRecords->filter(fn (GymCheckin $c) => $c->checked_in_at->isToday())->count(),
        'this_week_checkins'  => $checkinRecords->filter(fn (GymCheckin $c) => $c->checked_in_at->between(now()->startOfWeek(), now()->endOfWeek()))->count(),
        'latest_activity'     => $checkinRecords->first(),
    ];

    $reportDateLabel = $detailFilterType === 'range'
        ? $detailFrom->format('d M Y') . ' - ' . $detailTo->format('d M Y')
        : $detailMonth->translatedFormat('F Y');

    // ── Report catalog ────────────────────────────────────────────────────────
    $reportCatalog = collect([
        // 1. Aktivitas latihan
        [
            'title'   => 'Aktivitas latihan',
            'group'   => 'Operasional',
            'summary' => 'Ringkasan check-in member dan catatan aktivitas latihan terbaru.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredTrainingActivities->count() . ' aktivitas',
            'highlight'    => $dailyTrainingStats['today_checkins'] . ' check-in hari ini',
            'columns' => ['Nama Member', 'Tanggal', 'Jam', 'Status Paket', 'Catatan'],
            'rows'    => $filteredTrainingActivities->map(fn (GymCheckin $a) => [
                $a->member?->full_name ?? '-',
                $a->checked_in_at->format('d M Y'),
                $a->checked_in_at->format('H:i'),
                $a->member?->package_status === 'active' ? 'Aktif' : 'Expired',
                $a->notes ?: 'Latihan member',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Total Check-in', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($checkinRecords) {
                $count = $checkinRecords->filter(fn (GymCheckin $a) => $a->checked_in_at->isSameDay($date))->count();
                return [$date->format('d M Y'), $count . ' check-in', $count > 0 ? 'Aktivitas member tercatat di hari ini.' : 'Belum ada aktivitas tercatat.'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Total Check-in', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($checkinRecords) {
                $count = $checkinRecords->filter(fn (GymCheckin $a) => $a->checked_in_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->count();
                return [$month->translatedFormat('F Y'), $count . ' check-in', $count > 0 ? 'Traffic latihan tercatat pada bulan ini.' : 'Belum ada check-in di bulan ini.'];
            }),
        ],

        // 2. Ringkasan membership
        [
            'title'   => 'Ringkasan membership',
            'group'   => 'Operasional',
            'summary' => 'Pantau status aktif, akan berakhir, dan expired untuk kebutuhan follow-up.',
            'date_label'   => 'Per ' . now()->format('d M Y'),
            'count_label'  => $filteredMembershipRows->count() . ' member',
            'highlight'    => $membershipReportSummary['ending'] . ' akan berakhir',
            'columns' => ['Nama Member', 'Status', 'Metode Bayar', 'Tanggal Daftar', 'Masa Aktif Sampai'],
            'rows'    => $filteredMembershipRows->map(fn (array $row) => [
                $row['name'], $row['status'], $row['payment_method'],
                $row['joined_at']?->format('d M Y') ?: '-',
                $row['expires_at']?->format('d M Y') ?: '-',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Member Baru', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($memberRecords) {
                $count = $memberRecords->filter(fn (GymMember $m) => $m->joined_at?->isSameDay($date))->count();
                return [$date->format('d M Y'), $count . ' member', $count > 0 ? 'Member baru bergabung pada hari ini.' : 'Tidak ada member baru pada hari ini.'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Member Baru', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($memberRecords) {
                $count = $memberRecords->filter(fn (GymMember $m) => $m->joined_at?->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->count();
                return [$month->translatedFormat('F Y'), $count . ' member', $count > 0 ? 'Pertumbuhan member tercatat pada bulan ini.' : 'Tidak ada member baru pada bulan ini.'];
            }),
        ],

        // 3. Pembayaran member
        [
            'title'   => 'Pembayaran member',
            'group'   => 'Transaksi',
            'summary' => 'Daftar pembayaran member dari kasir yang sudah masuk ke sistem.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredMemberPayments->count() . ' transaksi',
            'highlight'    => 'Rp' . number_format($filteredMemberPayments->sum('amount'), 0, ',', '.'),
            'columns' => ['Invoice', 'Nama Member', 'Paket', 'Nominal', 'Metode', 'Status'],
            'rows'    => $filteredMemberPayments->map(fn (CashierTransaction $p) => [
                $p->invoice, $p->customer_name, $p->transaction_type,
                'Rp' . number_format($p->amount, 0, ',', '.'),
                strtoupper($p->payment_method),
                $p->payment_status === 'verified' ? 'Terverifikasi' : 'Pending',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Pendapatan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($memberPaymentRecords) {
                $tx  = $memberPaymentRecords->filter(fn (CashierTransaction $p) => $p->transaction_at->isSameDay($date) && $p->payment_status === 'verified');
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pendapatan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($memberPaymentRecords) {
                $tx  = $memberPaymentRecords->filter(fn (CashierTransaction $p) => $p->payment_status === 'verified' && $p->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 4. Pembayaran non-member
        [
            'title'   => 'Pembayaran non-member',
            'group'   => 'Transaksi',
            'summary' => 'Daftar pembayaran daily pass dan kunjungan non-member.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredNonMemberPayments->count() . ' transaksi',
            'highlight'    => 'Rp' . number_format($filteredNonMemberPayments->sum('amount'), 0, ',', '.'),
            'columns' => ['Invoice', 'Nama Pengunjung', 'Tipe', 'Nominal', 'Metode', 'Status'],
            'rows'    => $filteredNonMemberPayments->map(fn (CashierTransaction $p) => [
                $p->invoice, $p->customer_name, $p->transaction_type,
                'Rp' . number_format($p->amount, 0, ',', '.'),
                strtoupper($p->payment_method),
                $p->payment_status === 'verified' ? 'Terverifikasi' : 'Pending',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Pendapatan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($nonMemberPaymentRecords) {
                $tx = $nonMemberPaymentRecords->filter(fn (CashierTransaction $p) => $p->transaction_at->isSameDay($date) && $p->payment_status === 'verified');
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pendapatan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($nonMemberPaymentRecords) {
                $tx = $nonMemberPaymentRecords->filter(fn (CashierTransaction $p) => $p->payment_status === 'verified' && $p->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 5. Penjualan produk
        [
            'title'   => 'Penjualan produk',
            'group'   => 'Transaksi',
            'summary' => 'Daftar transaksi penjualan produk yang diterima oleh kasir.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredProductSales->count() . ' transaksi',
            'highlight'    => 'Rp' . number_format($filteredProductSales->sum('amount'), 0, ',', '.'),
            'columns' => ['Invoice', 'Nama Pelanggan', 'Produk', 'Jumlah', 'Nominal', 'Metode', 'Status'],
            'rows'    => $filteredProductSales->map(fn (CashierTransaction $p) => [
                $p->invoice, $p->customer_name, $p->product?->name ?? $p->transaction_type, $p->quantity ?? 1,
                'Rp' . number_format($p->amount, 0, ',', '.'), strtoupper($p->payment_method),
                $p->payment_status === 'verified' ? 'Terverifikasi' : 'Pending',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Pendapatan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($productSaleRecords) {
                $tx = $productSaleRecords->filter(fn (CashierTransaction $p) => $p->transaction_at->isSameDay($date) && $p->payment_status === 'verified');
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pendapatan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($productSaleRecords) {
                $tx = $productSaleRecords->filter(fn (CashierTransaction $p) => $p->payment_status === 'verified' && $p->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 6. Transaksi lain
        [
            'title'   => 'Transaksi lain',
            'group'   => 'Transaksi',
            'summary' => 'Catatan pemasukan lain di luar membership dan daily pass.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredOtherTransactions->count() . ' transaksi',
            'highlight'    => 'Rp' . number_format($filteredOtherTransactions->sum('amount'), 0, ',', '.'),
            'columns' => ['Invoice', 'Pelanggan', 'Jenis Transaksi', 'Nominal', 'Metode', 'Status'],
            'rows'    => $filteredOtherTransactions->map(fn (CashierTransaction $p) => [
                $p->invoice, $p->customer_name, $p->transaction_type,
                'Rp' . number_format($p->amount, 0, ',', '.'), strtoupper($p->payment_method),
                $p->payment_status === 'verified' ? 'Terverifikasi' : 'Pending',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Pendapatan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($otherTransactionRecords) {
                $tx = $otherTransactionRecords->filter(fn (CashierTransaction $p) => $p->transaction_at->isSameDay($date) && $p->payment_status === 'verified');
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pendapatan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($otherTransactionRecords) {
                $tx = $otherTransactionRecords->filter(fn (CashierTransaction $p) => $p->payment_status === 'verified' && $p->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 7. Laporan keuangan bulanan
        [
            'title'   => 'Laporan keuangan bulanan',
            'group'   => 'Keuangan',
            'summary' => 'Ringkasan pemasukan, pengeluaran, dan laba bersih bulan berjalan.',
            'date_label'   => $financialSummary['month_label'],
            'count_label'  => $financialSummary['month_label'],
            'highlight'    => 'Rp' . number_format($financialSummary['net_revenue'], 0, ',', '.'),
            'columns' => ['Keterangan', 'Nilai'],
            'rows'    => [
                ['Pemasukan member',     'Rp' . number_format($financialSummary['member_revenue'], 0, ',', '.')],
                ['Pemasukan non-member', 'Rp' . number_format($financialSummary['non_member_revenue'], 0, ',', '.')],
                ['Transaksi lain',       'Rp' . number_format($financialSummary['other_revenue'], 0, ',', '.')],
                ['Total pemasukan',      'Rp' . number_format($financialSummary['total_revenue'], 0, ',', '.')],
                ['Total pengeluaran',    'Rp' . number_format($financialSummary['total_expense'], 0, ',', '.')],
                ['Laba bersih',          'Rp' . number_format($financialSummary['net_revenue'], 0, ',', '.')],
            ],
            'daily_stat_columns' => ['Tanggal', 'Pemasukan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($verifiedTransactions, $expenseRecords) {
                $rev = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isSameDay($date))->sum('amount');
                $exp = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->isSameDay($date))->sum('amount');
                return [$date->format('d M Y'), 'Rp' . number_format($rev, 0, ',', '.'), 'Pengeluaran Rp' . number_format($exp, 0, ',', '.') . ' • Laba bersih Rp' . number_format($rev - $exp, 0, ',', '.')];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pemasukan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($verifiedTransactions, $expenseRecords) {
                $rev = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->sum('amount');
                $exp = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->sum('amount');
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($rev, 0, ',', '.'), 'Pengeluaran Rp' . number_format($exp, 0, ',', '.') . ' • Laba bersih Rp' . number_format($rev - $exp, 0, ',', '.')];
            }),
        ],

        // 8. Breakdown pemasukan
        [
            'title'   => 'Breakdown pemasukan',
            'group'   => 'Keuangan',
            'summary' => 'Komposisi pemasukan berdasarkan jenis transaksi terverifikasi.',
            'date_label'   => $financialSummary['month_label'],
            'count_label'  => $financialRevenueBreakdown->count() . ' kategori',
            'highlight'    => 'Rp' . number_format($financialRevenueBreakdown->sum('amount'), 0, ',', '.'),
            'columns' => ['Jenis', 'Deskripsi', 'Jumlah Transaksi', 'Nominal'],
            'rows'    => $financialRevenueBreakdown->map(fn (array $i) => [$i['label'], $i['description'], $i['count'], 'Rp' . number_format($i['amount'], 0, ',', '.')])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Total Pemasukan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isSameDay($date));
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Total Pemasukan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 9. Pendapatan per hari
        [
            'title'   => 'Pendapatan per hari',
            'group'   => 'Keuangan',
            'summary' => 'Rincian pemasukan harian agar admin lebih mudah membaca performa pendapatan setiap tanggal.',
            'date_label'   => $financialSummary['month_label'],
            'count_label'  => $financialDailyRevenue->count() . ' hari',
            'highlight'    => 'Rp' . number_format($financialDailyRevenue->max('total_revenue') ?? 0, 0, ',', '.'),
            'columns' => ['Tanggal', 'Pemasukan Member', 'Pemasukan Non-Member', 'Transaksi Lain', 'Total Pendapatan', 'Jumlah Transaksi'],
            'rows'    => $financialDailyRevenue->map(fn (array $i) => [
                $i['date']->format('d M Y'),
                'Rp' . number_format($i['member_revenue'], 0, ',', '.'),
                'Rp' . number_format($i['non_member_revenue'], 0, ',', '.'),
                'Rp' . number_format($i['other_revenue'], 0, ',', '.'),
                'Rp' . number_format($i['total_revenue'], 0, ',', '.'),
                $i['transaction_count'] . ' transaksi',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Total Pendapatan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isSameDay($date));
                return [$date->format('d M Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Total Pendapatan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($tx->sum('amount'), 0, ',', '.'), $tx->count() . ' transaksi terverifikasi'];
            }),
        ],

        // 10. Breakdown pengeluaran
        [
            'title'   => 'Breakdown pengeluaran',
            'group'   => 'Keuangan',
            'summary' => 'Komposisi pengeluaran per kategori operasional.',
            'date_label'   => $financialSummary['month_label'],
            'count_label'  => $financialExpenseBreakdown->count() . ' kategori',
            'highlight'    => 'Rp' . number_format($financialExpenseBreakdown->sum('amount'), 0, ',', '.'),
            'columns' => ['Kategori', 'Jumlah Catatan', 'Nominal'],
            'rows'    => $financialExpenseBreakdown->map(fn (array $i) => [$i['category'], $i['count'], 'Rp' . number_format($i['amount'], 0, ',', '.')])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Total Pengeluaran', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($expenseRecords) {
                $items = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->isSameDay($date));
                return [$date->format('d M Y'), 'Rp' . number_format($items->sum('amount'), 0, ',', '.'), $items->count() . ' catatan pengeluaran'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Total Pengeluaran', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($expenseRecords) {
                $items = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($items->sum('amount'), 0, ',', '.'), $items->count() . ' catatan pengeluaran'];
            }),
        ],

        // 11. Metode pembayaran
        [
            'title'   => 'Metode pembayaran',
            'group'   => 'Keuangan',
            'summary' => 'Lihat kontribusi pembayaran berdasarkan metode yang digunakan.',
            'date_label'   => $financialSummary['month_label'],
            'count_label'  => $financialPaymentMethodBreakdown->count() . ' metode',
            'highlight'    => $financialPaymentMethodBreakdown->first()['method'] ?? '-',
            'columns' => ['Metode', 'Jumlah Transaksi', 'Nominal'],
            'rows'    => $financialPaymentMethodBreakdown->map(fn (array $i) => [$i['method'], $i['count'], 'Rp' . number_format($i['amount'], 0, ',', '.')])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Metode Dominan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isSameDay($date));
                $top = $tx->groupBy(fn (CashierTransaction $t) => strtoupper($t->payment_method ?: '-'))->map(fn ($g) => $g->sum('amount'))->sortDesc()->keys()->first() ?? '-';
                return [$date->format('d M Y'), $top, 'Rp' . number_format($tx->sum('amount'), 0, ',', '.') . ' dari ' . $tx->count() . ' transaksi'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Metode Dominan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($verifiedTransactions) {
                $tx = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                $top = $tx->groupBy(fn (CashierTransaction $t) => strtoupper($t->payment_method ?: '-'))->map(fn ($g) => $g->sum('amount'))->sortDesc()->keys()->first() ?? '-';
                return [$month->translatedFormat('F Y'), $top, 'Rp' . number_format($tx->sum('amount'), 0, ',', '.') . ' dari ' . $tx->count() . ' transaksi'];
            }),
        ],

        // 12. Histori keuangan bulanan
        [
            'title'   => 'Histori keuangan bulanan',
            'group'   => 'Keuangan',
            'summary' => 'Perbandingan pendapatan, pengeluaran, dan laba bersih beberapa bulan terakhir.',
            'date_label'   => ($financialMonthlyHistory->first()['month_label'] ?? '-') . ' - ' . ($financialMonthlyHistory->last()['month_label'] ?? '-'),
            'count_label'  => $financialMonthlyHistory->count() . ' bulan',
            'highlight'    => $financialMonthlyHistory->last()['month_label'] ?? '-',
            'columns' => ['Bulan', 'Total Pemasukan', 'Total Pengeluaran', 'Laba Bersih', 'Catatan'],
            'rows'    => $financialMonthlyHistory->map(fn (array $i) => [
                $i['month_label'],
                'Rp' . number_format($i['total_revenue'], 0, ',', '.'),
                'Rp' . number_format($i['total_expense'], 0, ',', '.'),
                'Rp' . number_format($i['net_revenue'], 0, ',', '.'),
                $i['transaction_count'] . ' transaksi • ' . $i['expense_count'] . ' pengeluaran',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Pemasukan', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($verifiedTransactions, $expenseRecords) {
                $rev = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isSameDay($date))->sum('amount');
                $exp = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->isSameDay($date))->sum('amount');
                return [$date->format('d M Y'), 'Rp' . number_format($rev, 0, ',', '.'), 'Pengeluaran Rp' . number_format($exp, 0, ',', '.') . ' • Laba bersih Rp' . number_format($rev - $exp, 0, ',', '.')];
            }),
            'monthly_stat_columns' => ['Bulan', 'Pemasukan', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($verifiedTransactions, $expenseRecords) {
                $rev = $verifiedTransactions->filter(fn (CashierTransaction $t) => $t->transaction_at->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->sum('amount');
                $exp = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()))->sum('amount');
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($rev, 0, ',', '.'), 'Pengeluaran Rp' . number_format($exp, 0, ',', '.') . ' • Laba bersih Rp' . number_format($rev - $exp, 0, ',', '.')];
            }),
        ],

        // 13. Pengeluaran operasional
        [
            'title'   => 'Pengeluaran operasional',
            'group'   => 'Keuangan',
            'summary' => 'Daftar pengeluaran operasional terbaru untuk audit internal.',
            'date_label'   => $reportDateLabel,
            'count_label'  => $filteredExpenseRecords->count() . ' catatan',
            'highlight'    => 'Rp' . number_format($filteredExpenseRecords->sum('amount'), 0, ',', '.'),
            'columns' => ['Tanggal', 'Nama Pengeluaran', 'Kategori', 'Metode', 'Nominal', 'Catatan'],
            'rows'    => $filteredExpenseRecords->map(fn (ExpenseRecord $e) => [
                $e->expense_date->format('d M Y'),
                $e->title,
                Str::of($e->category)->replace('_', ' ')->title()->value(),
                strtoupper($e->payment_method ?: '-'),
                'Rp' . number_format($e->amount, 0, ',', '.'),
                $e->notes ?: '-',
            ])->values()->all(),
            'daily_stat_columns' => ['Tanggal', 'Total Pengeluaran', 'Catatan'],
            'daily_stat_rows'    => $buildDailyStatRows(function (Carbon $date) use ($expenseRecords) {
                $items = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->isSameDay($date));
                return [$date->format('d M Y'), 'Rp' . number_format($items->sum('amount'), 0, ',', '.'), $items->count() . ' catatan pengeluaran'];
            }),
            'monthly_stat_columns' => ['Bulan', 'Total Pengeluaran', 'Catatan'],
            'monthly_stat_rows'    => $buildMonthlyStatRows(function (Carbon $month) use ($expenseRecords) {
                $items = $expenseRecords->filter(fn (ExpenseRecord $e) => $e->expense_date->betweenIncluded($month->copy()->startOfMonth(), $month->copy()->endOfMonth()));
                return [$month->translatedFormat('F Y'), 'Rp' . number_format($items->sum('amount'), 0, ',', '.'), $items->count() . ' catatan pengeluaran'];
            }),
        ],
    ])
    ->map(function (array $report) {
        $report['slug']    = Str::slug($report['title']);
        $report['preview'] = collect($report['rows'])->first()
            ? collect(collect($report['rows'])->first())->implode(' • ')
            : 'Belum ada data.';
        return $report;
    })
    ->values();

    $membershipAlertTitle = $endingSoonMembers->count() > 0
        ? $endingSoonMembers->count() . ' Member Akan Berakhir'
        : 'Belum Ada Alert Membership';
    $membershipAlertNote  = $endingSoonMembers->count() > 0
        ? 'Segera hubungi member yang masa aktifnya hampir habis.'
        : 'Status membership saat ini masih aman.';

    return [
        'dailyTrainingStats'      => $dailyTrainingStats,
        'financialSummary'        => $financialSummary,
        'sidebarExtraSummary'     => ['label' => 'Ringkasan Cepat', 'title' => 'Laporan Hari Ini', 'note' => 'Pantau check-in, membership, pemasukan, dan pengeluaran dari sidebar tanpa perlu scroll ke bawah.'],
        'sidebarExtraItemsTitle'  => 'Fokus Tindak Lanjut',
        'sidebarExtraItems'       => [
            ['title' => $membershipAlertTitle, 'note' => $membershipAlertNote],
            ['title' => 'Rp' . number_format($financialSummary['total_revenue'], 0, ',', '.') . ' Pemasukan ' . $financialSummary['month_label'], 'note' => 'Bandingkan dengan pengeluaran bulan berjalan agar margin operasional tetap sehat.'],
            ['title' => $dailyTrainingStats['today_checkins'] . ' Check-in Hari Ini', 'note' => 'Gunakan angka ini untuk membaca traffic latihan harian gym.'],
        ],
        'membershipReportSummary' => $membershipReportSummary,
        'recentTrainingActivities'=> $filteredTrainingActivities,
        'membershipOperationalRows'=> $filteredMembershipRows,
        'financialRevenueBreakdown'=> $financialRevenueBreakdown,
        'financialExpenseBreakdown'=> $financialExpenseBreakdown,
        'financialPaymentMethodBreakdown' => $financialPaymentMethodBreakdown,
        'financialMonthlyHistory' => $financialMonthlyHistory,
        'expenseRecords'          => $filteredExpenseRecords,
        'memberPaymentRecords'    => $filteredMemberPayments,
        'nonMemberPaymentRecords' => $filteredNonMemberPayments,
        'otherTransactionRecords' => $filteredOtherTransactions,
        'visitorDailyRows'        => $visitorDailyRows,
        'reportCatalog'           => $reportCatalog,
        'detailActiveTab'         => $activeDetailTab,
        'detailFilterType'        => $detailFilterType,
        'detailFilterMonth'       => $detailMonth->format('Y-m'),
        'detailFilterFrom'        => $detailFrom->format('Y-m-d'),
        'detailFilterTo'          => $detailTo->format('Y-m-d'),
        'detailFilterLabel'       => $detailFilterType === 'range'
            ? $detailFrom->format('d M Y') . ' - ' . $detailTo->format('d M Y')
            : $detailMonth->translatedFormat('F Y'),
    ];
};

// ─────────────────────────────────────────────────────────────────────────────
// Helper: build export response (XLS via HTML table)
// ─────────────────────────────────────────────────────────────────────────────
$buildAdminReportExportResponse = function (array $selectedReport, string $detailFilterMonth) {
    $filename = 'laporan-' . Str::slug($selectedReport['title']) . '-' . $detailFilterMonth . '.xls';

    $tableRows = collect($selectedReport['rows'])
        ->map(fn (array $row) => '<tr>' . collect($row)->map(fn ($c) => '<td>' . e((string) $c) . '</td>')->implode('') . '</tr>')
        ->implode('');

    $headerColumns = collect($selectedReport['columns'])
        ->map(fn (string $col) => '<th>' . e($col) . '</th>')
        ->implode('');

    $buildStatTable = function (string $title, array $columns, array $rows): string {
        $header = collect($columns)->map(fn ($c) => '<th>' . e($c) . '</th>')->implode('');
        $body   = collect($rows)->map(fn (array $row) => '<tr>' . collect($row)->map(fn ($c) => '<td>' . e((string) $c) . '</td>')->implode('') . '</tr>')->implode('');
        return '<h2 style="font-size:16px;margin:28px 0 8px;">' . e($title) . '</h2>'
            . '<table><thead><tr>' . $header . '</tr></thead><tbody>' . $body . '</tbody></table>';
    };

    $content =
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">'
        . '<head><meta charset="utf-8"><style>body{font-family:Calibri,Arial,sans-serif;font-size:12px;color:#111827}h1{font-size:20px;margin-bottom:6px}p{margin:0 0 6px}table{border-collapse:collapse;width:100%;margin-top:16px}th,td{border:1px solid #cbd5e1;padding:8px 10px;vertical-align:top}th{background:#e2e8f0;font-weight:700}.meta{margin-top:8px;color:#475569}</style></head>'
        . '<body>'
        . '<h1>' . e($selectedReport['title']) . '</h1>'
        . '<p>' . e($selectedReport['summary']) . '</p>'
        . '<p class="meta">Kategori: ' . e($selectedReport['group']) . ' | Tanggal laporan: ' . e($selectedReport['date_label']) . ' | Data: ' . e($selectedReport['count_label']) . ' | Highlight: ' . e($selectedReport['highlight']) . '</p>'
        . '<table><thead><tr>' . $headerColumns . '</tr></thead><tbody>' . $tableRows . '</tbody></table>'
        . $buildStatTable('Statistik Harian Bulan Aktif',  $selectedReport['daily_stat_columns']   ?? ['Tanggal', 'Statistik', 'Catatan'], $selectedReport['daily_stat_rows']   ?? [])
        . $buildStatTable('Statistik Bulanan Tahun Aktif', $selectedReport['monthly_stat_columns'] ?? ['Bulan',   'Statistik', 'Catatan'], $selectedReport['monthly_stat_rows'] ?? [])
        . '</body></html>';

    return response($content, 200, [
        'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
};

// ─────────────────────────────────────────────────────────────────────────────
// Routes
// ─────────────────────────────────────────────────────────────────────────────

// ── Halaman laporan utama ─────────────────────────────────────────────────────
Route::get('/reports', function (Request $request) use ($buildAdminReportsData) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $reportData = $buildAdminReportsData($request);

    return view('admin.reports', array_merge(RouteHelpers::pageMeta('reports'), $reportData));
})->name('reports');

// ── Detail / ekspor laporan ───────────────────────────────────────────────────
Route::get('/reports/{reportSlug}', function (Request $request, string $reportSlug) use ($buildAdminReportsData, $buildAdminReportExportResponse) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $reportData     = $buildAdminReportsData($request);
    $selectedReport = collect($reportData['reportCatalog'])->firstWhere('slug', $reportSlug);

    abort_unless($selectedReport, 404);

    if ($request->boolean('export')) {
        return $buildAdminReportExportResponse($selectedReport, $reportData['detailFilterMonth']);
    }

    return view('admin.report-detail', array_merge(RouteHelpers::pageMeta('reports'), $reportData, [
        'selectedReport' => $selectedReport,
    ]));
})->name('reports.show');

// ── Tambah pengeluaran operasional ────────────────────────────────────────────
Route::post('/reports/expenses', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title'          => ['required', 'string', 'max:255'],
        'category'       => ['required', 'string', 'max:60'],
        'amount'         => ['required', 'integer', 'min:1'],
        'payment_method' => ['nullable', 'in:cash,qris'],
        'expense_date'   => ['required', 'date'],
        'notes'          => ['nullable', 'string'],
    ]);

    ExpenseRecord::create($validated);

    return back()->with('status', 'Pengeluaran berhasil ditambahkan ke laporan.');
})->name('reports.expenses.store');
