<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Admin – Laporan & Ekspor
|--------------------------------------------------------------------------
*/

$buildAdminReportsData = function (Request $request): array {
    // Ambil data dasar
    $memberRecords      = GymMember::query()->get();
    $dailyPassRecords   = DailyGuest::query()->get();
    $checkinRecords     = GymCheckin::query()->with('member')->where('verification_status', 'verified')->latest('checked_in_at')->get();
    $cashierTransactions= CashierTransaction::query()->with(['member', 'product'])->latest('transaction_at')->get();
    $expenseRecords     = ExpenseRecord::query()->latest('expense_date')->latest()->get();
    $vitaminProductRecords = Product::query()
        ->where('category', 'vitamin')
        ->orderBy('name')
        ->get();

    $verifiedTransactions    = $cashierTransactions->filter(fn (CashierTransaction $t) => $t->payment_status === 'verified');
    $memberPaymentRecords     = $cashierTransactions->where('transaction_group', 'member_payment')->values();
    $dailyPassPaymentRecords  = $cashierTransactions->where('transaction_group', 'daily_pass')->values();
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
    $activeDetailTab = in_array($request->query('detail_tab'), ['activity', 'membership', 'member-payments', 'daily-pass-payments', 'other-transactions', 'expenses'], true) ? $request->query('detail_tab') : 'activity';
    $detailFilterType = in_array($request->query('detail_filter'), ['month', 'range'], true) ? $request->query('detail_filter') : 'month';
    $detailMonthInput = (string) $request->query('detail_month', now()->format('Y-m'));
    $detailMonth      = preg_match('/^\d{4}-\d{2}$/', $detailMonthInput) ? Carbon::createFromFormat('Y-m', $detailMonthInput)->startOfMonth() : now()->startOfMonth();

    $detailFrom = $request->filled('detail_from') ? Carbon::parse($request->query('detail_from'))->startOfDay() : $detailMonth->copy()->startOfMonth();
    $detailTo   = $request->filled('detail_to') ? Carbon::parse($request->query('detail_to'))->endOfDay() : $detailMonth->copy()->endOfMonth();

    if ($detailTo->lt($detailFrom)) {
        [$detailFrom, $detailTo] = [$detailTo->copy()->startOfDay(), $detailFrom->copy()->endOfDay()];
    }

    $detailRangeStart = $detailFilterType === 'range' ? $detailFrom->copy()->startOfDay() : $detailMonth->copy()->startOfMonth();
    $detailRangeEnd   = $detailFilterType === 'range' ? $detailTo->copy()->endOfDay()     : $detailMonth->copy()->endOfMonth();

    $filteredMemberRecords = $memberRecords;

    $isDateIncluded = function ($date) use ($detailFilterType, $detailFrom, $detailTo, $detailMonth): bool {
        if (! $date) return false;
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $detailFilterType === 'range' ? $date->betweenIncluded($detailFrom, $detailTo) : $date->betweenIncluded($detailMonth->copy()->startOfMonth(), $detailMonth->copy()->endOfMonth());
    };

    $reportDateLabel = $detailFilterType === 'range' ? $detailFrom->format('d M Y') . ' - ' . $detailTo->format('d M Y') : $detailMonth->translatedFormat('F Y');

    // ── Financial summary ─────────────────────────────────────────────────────
    $reportMonthStart = $detailMonth->copy()->startOfMonth();
    $reportMonthEnd   = $detailMonth->copy()->endOfMonth();

    $monthlyVerifiedTransactions = $verifiedTransactions->filter(fn ($t) => $t->transaction_at->between($reportMonthStart, $reportMonthEnd))->values();
    $monthlyExpenseRecords = $expenseRecords->filter(fn ($e) => $e->expense_date->between($reportMonthStart, $reportMonthEnd))->values();

    $financialSummary = [
        'month_label'               => $reportMonthStart->translatedFormat('F Y'),
        'total_revenue'             => $monthlyVerifiedTransactions->sum('amount'),
        'total_expense'             => $monthlyExpenseRecords->sum('amount'),
        'net_revenue'               => $monthlyVerifiedTransactions->sum('amount') - $monthlyExpenseRecords->sum('amount'),
        'member_revenue'            => $monthlyVerifiedTransactions->where('transaction_group', 'member_payment')->sum('amount'),
        'daily_pass_revenue'        => $monthlyVerifiedTransactions->where('transaction_group', 'daily_pass')->sum('amount'),
        'other_revenue'             => $monthlyVerifiedTransactions->where('transaction_group', 'other')->sum('amount'),
        'verified_transaction_count'=> $monthlyVerifiedTransactions->count(),
        'expense_count'             => $monthlyExpenseRecords->count(),
    ];

    // ── Training Stats ────────────────────────────────────────────────────────
    $dailyTrainingStats = [
        'today_checkins' => $checkinRecords->filter(fn($c) => $c->checked_in_at->isToday())->count(),
    ];

    // ── Histori Bulanan ───────────────────────────────────────────────────────
    $financialMonthlyHistory = collect(range(0, 5))->map(function (int $offset) use ($verifiedTransactions, $expenseRecords) {
        $monthStart = now()->copy()->subMonths($offset)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();
        $mRev = $verifiedTransactions->filter(fn ($t) => $t->transaction_at->between($monthStart, $monthEnd))->sum('amount');
        $mExp = $expenseRecords->filter(fn ($e) => $e->expense_date->between($monthStart, $monthEnd))->sum('amount');
        return ['month_label' => $monthStart->translatedFormat('F Y'), 'total_revenue' => $mRev, 'total_expense' => $mExp, 'net_revenue' => $mRev - $mExp];
    })->values()->reverse()->values();

    // ── Visitor Statistics ────────────────────────────────────────────────────
    $visitorDailyRows = collect(); // Logic dipersingkat untuk performa
    for ($date = $detailRangeStart->copy(); $date->lte($detailRangeEnd); $date->addDay()) {
        $visitorDailyRows->push(['date' => $date->copy()]);
    }

    $buildDailyRows = function (Carbon $start, Carbon $end, callable $callback) {
        $rows = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $rows[] = $callback($date);
        }
        return $rows;
    };

    $buildMonthlyRows = function (int $year, callable $callback) {
        $rows = [];
        for ($month = 1; $month <= 12; $month++) {
            $rows[] = $callback(Carbon::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $year, $month)));
        }
        return $rows;
    };

    $formatMoney = fn(int $amount): string => 'Rp' . number_format($amount, 0, ',', '.');

    $reportCatalog = collect([
        [
            'title'       => 'Laporan Member',
            'group'       => 'Operasional',
            'summary'     => 'Ringkasan keanggotaan member, status aktif, member baru, dan expired.',
            'date_label'  => 'Per ' . now()->format('d M Y'),
            'count_label' => $memberRecords->count() . ' member',
            'highlight'   => $membershipReportSummary['ending'] . ' perlu follow-up',
            'preview'     => 'Data membership lengkap',
            'details'     => ['Jumlah member aktif', 'Member baru', 'Member expired', 'Riwayat pendaftaran', 'Data paket member'],
        ],
        [
            'title'       => 'Laporan Keuangan',
            'group'       => 'Keuangan',
            'summary'     => 'Ringkasan pemasukan, pembayaran member, dan riwayat transaksi.',
            'date_label'  => $financialSummary['month_label'],
            'count_label' => $financialSummary['verified_transaction_count'] . ' transaksi',
            'highlight'   => 'Rp' . number_format($financialSummary['net_revenue'], 0, ',', '.'),
            'preview'     => 'Pendapatan dan pembayaran',
            'details'     => ['Total pemasukan', 'Pembayaran member', 'Riwayat pemasukan', 'Riwayat pengeluaran'],
        ],
        [
            'title'       => 'Laporan Kehadiran',
            'group'       => 'Operasional',
            'summary'     => 'Rekap check-in member untuk analisis kehadiran.',
            'date_label'  => $reportDateLabel,
            'count_label' => $checkinRecords->count() . ' check-in',
            'highlight'   => $dailyTrainingStats['today_checkins'] . ' check-in hari ini',
            'preview'     => 'Jam kehadiran member',
            'details'     => ['Jam check-in', 'Jumlah hadir per hari', 'Member hadir', 'Status kehadiran'],
        ],
        [
            'title'       => 'Laporan Stok Barang',
            'group'       => 'Operasional',
            'summary'     => 'Daftar stok vitamin yang tersimpan di database produk.',
            'date_label'  => 'Per ' . now()->format('d M Y'),
            'count_label' => $vitaminProductRecords->count() . ' vitamin',
            'highlight'   => $vitaminProductRecords->where('stock', '<', 3)->count() . ' stok rendah',
            'preview'     => 'Stok vitamin tersedia',
            'details'     => ['Nama vitamin', 'Brand dan SKU', 'Stok saat ini', 'Harga jual', 'Status produk'],
        ],
    ])->map(function ($report) {
        $report = array_merge([
            'daily_stat_columns'   => ['Tanggal', 'Jumlah'],
            'daily_stat_rows'      => [],
            'monthly_stat_columns' => ['Bulan', 'Jumlah'],
            'monthly_stat_rows'    => [],
            'columns'              => ['Informasi'],
            'rows'                 => [],
            'overview'             => [],
        ], $report);

        $report['slug'] = Str::slug($report['title']);
        return $report;
    })->map(function ($report) use ($memberRecords, $filteredMemberRecords, $checkinRecords, $detailMonth, $detailRangeStart, $detailRangeEnd, $buildDailyRows, $buildMonthlyRows, $formatMoney, $verifiedTransactions, $expenseRecords, $memberPaymentRecords, $dailyPassPaymentRecords, $productSaleRecords, $vitaminProductRecords) {
        $stockSales = $productSaleRecords
            ->filter(fn ($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd));

        $stockSalesByProduct = $stockSales
            ->groupBy('product_id')
            ->map(fn ($transactions) => [
                'quantity' => $transactions->sum('quantity'),
                'amount' => $transactions->sum('amount'),
            ]);

        $dummyDetails = match ($report['slug']) {
            'aktivitas-latihan' => [
                'daily_stat_columns' => ['Tanggal', 'Kehadiran'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) {
                    $count = 12 + ($date->day % 7) * 2;
                    return [$date->translatedFormat('d M'), $count];
                }),
                'monthly_stat_columns' => ['Bulan', 'Kehadiran'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) {
                    $count = 320 + ($date->month * 12);
                    return [$date->translatedFormat('M'), $count];
                }),
                'columns' => ['Tanggal', 'Member', 'Jenis Aktivitas', 'Status'],
                'rows' => [
                    ['01 ' . $detailMonth->translatedFormat('M Y'), 'Andi', 'Latihan Bebas', 'Selesai'],
                    ['02 ' . $detailMonth->translatedFormat('M Y'), 'Budi', 'Kelas Strength', 'Selesai'],
                    ['03 ' . $detailMonth->translatedFormat('M Y'), 'Citra', 'Kartu Member', 'Dibatalkan'],
                ],
                'overview' => ['Kehadiran harian', 'Total check-in per hari', 'Detail aktivitas member'],
            ],
            'laporan-member' => [
                'daily_stat_columns' => ['Tanggal', 'Member Aktif', 'Member Baru', 'Expired'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) use ($memberRecords) {
                    $activeOnDate = $memberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->lte($date) && ($member->expires_at === null || $member->expires_at->gte($date)));
                    $newOnDate = $memberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->equalTo($date));
                    $expiredOnDate = $memberRecords->filter(fn($member) => $member->expires_at && $member->expires_at->equalTo($date));

                    $formatNames = fn($collection) => $collection->pluck('full_name')->take(3)->implode(', ') . ($collection->count() > 3 ? ' +'.($collection->count() - 3).' lain' : '');

                    return [
                        $date->translatedFormat('d M'),
                        $activeOnDate->count() . ($activeOnDate->count() ? ' (' . $formatNames($activeOnDate) . ')' : ''),
                        $newOnDate->count() . ($newOnDate->count() ? ' (' . $formatNames($newOnDate) . ')' : ''),
                        $expiredOnDate->count() . ($expiredOnDate->count() ? ' (' . $formatNames($expiredOnDate) . ')' : ''),
                    ];
                }),
                'monthly_stat_columns' => ['Bulan', 'Member Aktif', 'Member Baru', 'Expired'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) use ($memberRecords) {
                    $monthStart = $date->copy()->startOfMonth();
                    $monthEnd = $date->copy()->endOfMonth();
                    $activeInMonth = $memberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->lte($monthEnd) && ($member->expires_at === null || $member->expires_at->gte($monthStart)));
                    $newInMonth = $memberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->betweenIncluded($monthStart, $monthEnd));
                    $expiredInMonth = $memberRecords->filter(fn($member) => $member->expires_at && $member->expires_at->betweenIncluded($monthStart, $monthEnd));

                    $formatNames = fn($collection) => $collection->pluck('full_name')->take(3)->implode(', ') . ($collection->count() > 3 ? ' +'.($collection->count() - 3).' lain' : '');

                    return [
                        $date->translatedFormat('M'),
                        $activeInMonth->count() . ($activeInMonth->count() ? ' (' . $formatNames($activeInMonth) . ')' : ''),
                        $newInMonth->count() . ($newInMonth->count() ? ' (' . $formatNames($newInMonth) . ')' : ''),
                        $expiredInMonth->count() . ($expiredInMonth->count() ? ' (' . $formatNames($expiredInMonth) . ')' : ''),
                    ];
                }),
                'columns' => ['Nama Member', 'Jenis Keanggotaan', 'Status', 'Berakhir', 'Tanggal Daftar', 'Metode Pembayaran'],
                'rows' => $filteredMemberRecords->map(fn($member) => [
                    $member->full_name,
                    $member->payment_method ? ucfirst($member->payment_method) : 'Reguler',
                    $member->expires_at && $member->expires_at->gte(now()) ? 'Aktif' : 'Expired',
                    $member->expires_at?->format('d M Y') ?? '-',
                    $member->joined_at?->format('d M Y') ?? '-',
                    $member->payment_method ? ucfirst($member->payment_method) : 'Reguler',
                ])->take(10)->toArray(),
                'member_table_columns' => ['Nama Member', 'Metode Pembayaran', 'Tanggal Daftar', 'Berakhir'],
                'member_table_groups' => [
                    'Aktif' => $filteredMemberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->lte($detailRangeEnd) && ($member->expires_at === null || $member->expires_at->gte(now()))),
                    'Baru' => $filteredMemberRecords->filter(fn($member) => $member->joined_at && $member->joined_at->betweenIncluded($detailMonth->copy()->startOfMonth(), $detailMonth->copy()->endOfMonth())),
                    'Expired' => $filteredMemberRecords->filter(fn($member) => $member->expires_at && $member->expires_at->betweenIncluded($detailMonth->copy()->startOfMonth(), $detailMonth->copy()->endOfMonth())),
                ],
                'overview' => ['Jumlah member aktif', 'Member baru', 'Member expired', 'Riwayat pendaftaran', 'Data paket member'],
            ],
            'laporan-keuangan' => [
                'daily_stat_columns' => ['Tanggal', 'Total Pemasukan', 'Pembayaran Member', 'Jumlah Transaksi'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) {
                    $income = 1200000 + ($date->day * 8000);
                    $memberPayment = 700000 + ($date->day * 2500);
                    return [$date->translatedFormat('d M'), $income, $memberPayment, $date->day % 4 + 3 . ' transaksi'];
                }),
                'monthly_stat_columns' => ['Bulan', 'Total Pemasukan', 'Pembayaran Member', 'Transaksi'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) {
                    $income = 36000000 + ($date->month * 1000000);
                    $memberPayment = 21000000 + ($date->month * 500000);
                    return [$date->translatedFormat('M'), $income, $memberPayment, 520 + ($date->month * 10) . ' transaksi'];
                }),
                'columns' => ['Tanggal', 'Keterangan', 'Total Pemasukan', 'Pembayaran Member'],
                'rows' => [
                    [$detailMonth->copy()->startOfMonth()->format('d M Y'), 'Pembayaran member', 850000, 850000],
                    [$detailMonth->copy()->addDays(3)->format('d M Y'), 'Penjualan produk', 425000, 0],
                    [$detailMonth->copy()->addDays(5)->format('d M Y'), 'Pendapatan harian', 1200000, 0],
                ],
                'reportSections' => [
                    'Sumber Pendapatan' => [
                        'columns' => ['Sumber', 'Jumlah', 'Catatan'],
                        'rows' => [
                            ['Pembayaran member', $formatMoney($memberPaymentRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->sum('amount')), 'Pembayaran paket dan perpanjangan'],
                            ['Pembayaran daily pass', $formatMoney($dailyPassPaymentRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->sum('amount')), 'Daily pass dan pengunjung'],
                            ['Penjualan produk', $formatMoney($productSaleRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->sum('amount')), 'Suplemen, minuman, peralatan kecil'],
                        ],
                    ],
                    'Pembayaran' => [
                        'columns' => ['Tanggal Pembayaran', 'Nama', 'Kelompok', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $memberPaymentRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->take(5)->map(fn($transaction) => [
                            $transaction->transaction_at?->translatedFormat('d M Y') ?? '-',
                            $transaction->member?->full_name ?? $transaction->customer_name ?? 'Tidak dikenal',
                            'Member',
                            $formatMoney($transaction->amount),
                            ucfirst($transaction->payment_method ?? $transaction->transaction_type ?? 'Tunai'),
                        ])->toBase()->merge($dailyPassPaymentRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->take(5)->map(fn($transaction) => [
                            $transaction->transaction_at?->translatedFormat('d M Y') ?? '-',
                            $transaction->customer_name ?? 'Daily Pass',
                            'Daily Pass',
                            $formatMoney($transaction->amount),
                            ucfirst($transaction->payment_method ?? $transaction->transaction_type ?? 'Tunai'),
                        ])->toBase())->take(10)->values()->toArray(),
                    ],
                    'Penjualan Produk' => [
                        'columns' => ['Tanggal', 'Pelanggan', 'Produk', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $productSaleRecords->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->map(fn($transaction) => [
                            $transaction->transaction_at?->translatedFormat('d M Y') ?? '-',
                            $transaction->customer_name ?? 'Daily Pass',
                            $transaction->product?->name ?? ucfirst(str_replace('_', ' ', $transaction->transaction_group)),
                            $formatMoney($transaction->amount),
                            ucfirst($transaction->payment_method ?? $transaction->transaction_type ?? 'Tunai'),
                        ])->toBase()->values()->toArray(),
                    ],
                    'Riwayat Pemasukan' => [
                        'columns' => ['Tanggal', 'Sumber', 'Keterangan', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $verifiedTransactions->filter(fn($transaction) => $transaction->transaction_at && $transaction->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->map(fn($transaction) => [
                            $transaction->transaction_at->format('d M Y'),
                            ucfirst(str_replace('_', ' ', $transaction->transaction_group)),
                            $transaction->customer_name ?? $transaction->member?->full_name ?? 'Tidak dikenal',
                            $formatMoney($transaction->amount),
                            ucfirst($transaction->payment_method ?? $transaction->transaction_type ?? 'Tunai'),
                        ])->sortByDesc(fn($row) => Carbon::parse($row[0]))->values()->toArray(),
                    ],
                    'Riwayat Pengeluaran' => [
                        'columns' => ['Tanggal', 'Kategori', 'Keterangan', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $expenseRecords->filter(fn($expense) => $expense->expense_date && $expense->expense_date->betweenIncluded($detailRangeStart, $detailRangeEnd))->map(fn($expense) => [
                            $expense->expense_date->format('d M Y'),
                            ucfirst(str_replace('_', ' ', $expense->category ?? 'pengeluaran')),
                            $expense->title,
                            $formatMoney($expense->amount),
                            ucfirst($expense->payment_method ?? 'Tunai'),
                        ])->sortByDesc(fn($row) => Carbon::parse($row[0]))->values()->toArray(),
                    ],
                ],
                'overview' => ['Total pemasukan', 'Pembayaran member', 'Pembayaran daily pass', 'Riwayat pemasukan', 'Riwayat pengeluaran'],
            ],
            'laporan-kehadiran' => [
                'daily_stat_columns' => ['Tanggal', 'Jumlah Check-in', 'Member Hadir', 'Check-in Terakhir'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) use ($checkinRecords) {
                    $dailyCheckins = $checkinRecords->filter(fn($checkin) => $checkin->checked_in_at && $checkin->checked_in_at->isSameDay($date));
                    $memberNames = $dailyCheckins
                        ->map(fn($checkin) => $checkin->member?->full_name ?? $checkin->submitted_name ?? 'Tidak dikenal')
                        ->unique()
                        ->values();

                    return [
                        $date->translatedFormat('d M'),
                        $dailyCheckins->count(),
                        $memberNames->take(3)->implode(', ') . ($memberNames->count() > 3 ? ' +' . ($memberNames->count() - 3) . ' lain' : ''),
                        $dailyCheckins->sortByDesc('checked_in_at')->first()?->checked_in_at?->format('H:i') ?? '-',
                    ];
                }),
                'monthly_stat_columns' => ['Bulan', 'Jumlah Check-in', 'Member Unik', 'Rata-rata per Hari'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) use ($checkinRecords) {
                    $monthStart = $date->copy()->startOfMonth();
                    $monthEnd = $date->copy()->endOfMonth();
                    $monthlyCheckins = $checkinRecords->filter(fn($checkin) => $checkin->checked_in_at && $checkin->checked_in_at->betweenIncluded($monthStart, $monthEnd));
                    $uniqueMembers = $monthlyCheckins
                        ->map(fn($checkin) => $checkin->gym_member_id ?: ($checkin->submitted_name . '|' . $checkin->submitted_phone))
                        ->filter()
                        ->unique()
                        ->count();

                    return [
                        $date->translatedFormat('M'),
                        $monthlyCheckins->count(),
                        $uniqueMembers,
                        number_format($monthlyCheckins->count() / max($date->daysInMonth, 1), 1, ',', '.'),
                    ];
                }),
                'columns' => ['Tanggal', 'Nama Member', 'No. HP', 'Waktu Check-in', 'Metode', 'Status', 'Catatan'],
                'rows' => $checkinRecords
                    ->filter(fn($checkin) => $checkin->checked_in_at && $checkin->checked_in_at->betweenIncluded($detailRangeStart, $detailRangeEnd))
                    ->sortByDesc('checked_in_at')
                    ->map(fn($checkin) => [
                        $checkin->checked_in_at?->translatedFormat('d M Y') ?? '-',
                        $checkin->member?->full_name ?? $checkin->submitted_name ?? 'Tidak dikenal',
                        $checkin->member?->phone ?? $checkin->submitted_phone ?? '-',
                        $checkin->checked_in_at?->format('H:i') ?? '-',
                        ucfirst($checkin->checkin_method ?? 'admin'),
                        ucfirst($checkin->verification_status ?? 'verified'),
                        $checkin->notes ?: '-',
                    ])->values()->toArray(),
                'overview' => ['Daftar member check-in', 'Jumlah hadir per hari', 'Member unik bulanan', 'Status verifikasi'],
            ],
            'laporan-stok-barang' => [
                'daily_stat_columns' => ['Tanggal', 'Total Jenis Vitamin', 'Total Stok', 'Stok Rendah', 'Terjual'],
                'daily_stat_rows' => [[
                    now()->translatedFormat('d M'),
                    $vitaminProductRecords->count(),
                    $vitaminProductRecords->sum('stock'),
                    $vitaminProductRecords->where('stock', '<', 3)->count(),
                    $stockSales->sum('quantity'),
                ]],
                'monthly_stat_columns' => ['Bulan', 'Total Jenis Vitamin', 'Total Stok', 'Stok Rendah', 'Terjual'],
                'monthly_stat_rows' => [[
                    $detailMonth->translatedFormat('M'),
                    $vitaminProductRecords->count(),
                    $vitaminProductRecords->sum('stock'),
                    $vitaminProductRecords->where('stock', '<', 3)->count(),
                    $stockSales->sum('quantity'),
                ]],
                'columns' => ['Nama Vitamin', 'Brand', 'SKU', 'Stok', 'Terjual', 'Pendapatan Terjual', 'Status'],
                'rows' => $vitaminProductRecords->map(fn($product) => [
                    $product->name,
                    $product->brand ?: '-',
                    $product->sku ?: '-',
                    number_format($product->stock, 0, ',', '.') . ' ' . $product->unit,
                    $stockSalesByProduct->get($product->id)['quantity'] ?? 0,
                    $formatMoney($stockSalesByProduct->get($product->id)['amount'] ?? 0),
                    $product->is_active ? 'Aktif' : 'Nonaktif',
                ])->toArray(),
                'overview' => ['Nama vitamin', 'Brand dan SKU', 'Stok saat ini', 'Jumlah terjual', 'Pendapatan penjualan'],
                'detail_title' => 'Daftar stok vitamin',
                'reportSections' => [
                    'Rincian Penjualan' => [
                        'columns' => ['Produk', 'Jumlah Terjual', 'Pendapatan'],
                        'rows' => $vitaminProductRecords->map(fn($product) => [
                            $product->name,
                            $stockSalesByProduct->get($product->id)['quantity'] ?? 0,
                            $formatMoney($stockSalesByProduct->get($product->id)['amount'] ?? 0),
                        ])->toArray(),
                    ],
                    'Rincian Barang Terjual' => [
                        'columns' => ['Tanggal', 'Invoice', 'Pelanggan', 'Produk', 'Brand', 'SKU', 'Qty', 'Harga Satuan', 'Total', 'Pembayaran', 'Status'],
                        'rows' => $stockSales
                            ->sortByDesc('transaction_at')
                            ->map(fn($transaction) => [
                                $transaction->transaction_at?->translatedFormat('d M Y H:i') ?? '-',
                                $transaction->invoice ?: '-',
                                $transaction->customer_name ?: ($transaction->member?->full_name ?? 'Daily Pass'),
                                $transaction->product?->name ?? $transaction->transaction_type ?? 'Produk tidak dikenal',
                                $transaction->product?->brand ?: '-',
                                $transaction->product?->sku ?: '-',
                                number_format($transaction->quantity ?? 0, 0, ',', '.') . ' ' . ($transaction->product?->unit ?? 'pcs'),
                                $formatMoney((int) round(($transaction->amount ?? 0) / max((int) ($transaction->quantity ?? 1), 1))),
                                $formatMoney($transaction->amount ?? 0),
                                ucfirst($transaction->payment_method ?? 'Tunai'),
                                ucfirst($transaction->payment_status ?? '-'),
                            ])->values()->toArray(),
                    ],
                    'Produk Terlaris' => [
                        'columns' => ['Produk', 'Qty Terjual', 'Pendapatan'],
                        'rows' => collect($stockSalesByProduct)
                            ->sortByDesc(fn($stats) => $stats['quantity'])
                            ->take(10)
                            ->map(fn($stats, $productId) => [
                                $vitaminProductRecords->firstWhere('id', $productId)?->name ?? 'Produk tidak dikenal',
                                $stats['quantity'],
                                $formatMoney($stats['amount']),
                            ])->values()->toArray(),
                    ],
                ],
            ],
            default => [],
        };

        return array_merge($report, $dummyDetails);
    });

    return [
        'financialSummary'        => $financialSummary,
        'financialMonthlyHistory' => $financialMonthlyHistory,
        'dailyTrainingStats'      => $dailyTrainingStats,
        'membershipReportSummary' => $membershipReportSummary,
        'reportCatalog'           => $reportCatalog,
        'detailFilterMonth'       => $detailMonth->format('Y-m'),
        'detailFilterLabel'       => $reportDateLabel,
        'visitorDailyRows'        => $visitorDailyRows,
        // Tambahkan variabel kosong agar tidak error di bagian detail jika belum dipilih
        'recentTrainingActivities'=> collect(),
        'membershipOperationalRows'=> collect(),
        'expenseRecords'          => collect(),
        'memberPaymentRecords'    => collect(),
        'dailyPassPaymentRecords' => collect(),
        'otherTransactionRecords' => collect(),
    ];
};

// ── Routes ────────────────────────────────────────────────────────────────────
$buildExcelResponse = function (array $selectedReport, array $reportData) {
    $escape = fn($value): string => e((string) $value);

    $renderTable = function (string $title, array $columns, iterable $rows) use ($escape): string {
        $rows = collect($rows)->values();
        $colspan = max(count($columns), 1);
        $html = '<h2>' . $escape($title) . '</h2><table><thead><tr>';

        foreach ($columns as $column) {
            $html .= '<th>' . $escape($column) . '</th>';
        }

        $html .= '</tr></thead><tbody>';
        if ($rows->isEmpty()) {
            $html .= '<tr><td colspan="' . $colspan . '">Tidak ada data</td></tr>';
        } else {
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ((array) $row as $cell) {
                    $html .= '<td>' . $escape($cell) . '</td>';
                }
                $html .= '</tr>';
            }
        }

        return $html . '</tbody></table>';
    };

    $sections = [];
    $sections[] = $renderTable('Ringkasan', ['Keterangan', 'Nilai'], [
        ['Laporan', $selectedReport['title'] ?? '-'],
        ['Kategori', $selectedReport['group'] ?? '-'],
        ['Periode', $reportData['detailFilterLabel'] ?? '-'],
        ['Tanggal Laporan', $selectedReport['date_label'] ?? '-'],
        ['Jumlah Data', $selectedReport['count_label'] ?? '-'],
        ['Highlight', $selectedReport['highlight'] ?? '-'],
    ]);

    if (! empty($selectedReport['daily_stat_columns']) && ! empty($selectedReport['daily_stat_rows'])) {
        $sections[] = $renderTable('Statistik Harian', $selectedReport['daily_stat_columns'], $selectedReport['daily_stat_rows']);
    }

    if (! empty($selectedReport['monthly_stat_columns']) && ! empty($selectedReport['monthly_stat_rows'])) {
        $sections[] = $renderTable('Statistik Bulanan', $selectedReport['monthly_stat_columns'], $selectedReport['monthly_stat_rows']);
    }

    if (($selectedReport['slug'] ?? '') === 'laporan-member' && ! empty($selectedReport['member_table_groups'])) {
        foreach ($selectedReport['member_table_groups'] as $groupLabel => $groupMembers) {
            $rows = collect($groupMembers)->map(fn($member) => [
                $member->full_name,
                $member->payment_method ? ucfirst($member->payment_method) : 'Reguler',
                $member->joined_at?->format('d M Y') ?? '-',
                $member->expires_at?->format('d M Y') ?? '-',
            ]);

            $sections[] = $renderTable('Member ' . $groupLabel, $selectedReport['member_table_columns'], $rows);
        }
    } elseif (! empty($selectedReport['reportSections'])) {
        foreach ($selectedReport['reportSections'] as $sectionLabel => $sectionData) {
            $sections[] = $renderTable($sectionLabel, $sectionData['columns'] ?? [], $sectionData['rows'] ?? []);
        }
    } else {
        $sections[] = $renderTable($selectedReport['detail_title'] ?? 'Detail Laporan', $selectedReport['columns'] ?? [], $selectedReport['rows'] ?? []);
    }

    $filename = Str::slug($selectedReport['title'] ?? 'laporan') . '-' . now()->format('Ymd-His') . '.xls';
    $html = '<!doctype html><html><head><meta charset="UTF-8"><style>
        body { font-family: Calibri, Arial, sans-serif; color: #111827; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #111827; }
        h2 { font-size: 16px; margin: 22px 0 8px; color: #1f2937; }
        .meta { margin-bottom: 18px; color: #4b5563; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
        th { background: #111827; color: #ffffff; font-weight: 700; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 8px 10px; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        td { mso-number-format:"\@"; }
    </style></head><body>';
    $html .= '<h1>' . $escape($selectedReport['title'] ?? 'Laporan') . '</h1>';
    $html .= '<div class="meta">Arena Gym - Export ' . $escape(now()->format('d M Y H:i')) . '</div>';
    $html .= implode('', $sections);
    $html .= '</body></html>';

    return response($html, 200, [
        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
    ]);
};

$buildMemberExportResponse = function () {
    $escape = fn($value): string => e((string) $value);
    $members = GymMember::query()->orderBy('full_name')->get();
    $filename = 'data-member-' . now()->format('Ymd-His') . '.xls';
    $columns = ['Nama', 'Status', 'Paket', 'Metode Pembayaran', 'Tanggal Daftar', 'Berakhir', 'Catatan'];

    $html = '<!doctype html><html><head><meta charset="UTF-8"><style>
        body { font-family: Calibri, Arial, sans-serif; color: #111827; }
        h1 { font-size: 22px; margin: 0 0 16px; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #111827; color: #ffffff; font-weight: 700; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 8px 10px; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        td { mso-number-format:"\@"; }
    </style></head><body><h1>Data Member Arena Gym</h1><table><thead><tr>';

    foreach ($columns as $column) {
        $html .= '<th>' . $escape($column) . '</th>';
    }

    $html .= '</tr></thead><tbody>';
    foreach ($members as $member) {
        $html .= '<tr>';
        foreach ([
            $member->full_name,
            ucfirst(str_replace('_', ' ', $member->member_status ?? '-')),
            $member->membership_plan ?? '-',
            $member->payment_method ? ucfirst($member->payment_method) : '-',
            $member->joined_at?->format('d M Y') ?? '-',
            $member->expires_at?->format('d M Y') ?? '-',
            $member->notes ?? '-',
        ] as $cell) {
            $html .= '<td>' . $escape($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table></body></html>';

    return response($html, 200, [
        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
    ]);
};

Route::get('/reports', function (Request $request) use ($buildAdminReportsData) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    return view('admin.reports', array_merge(RouteHelpers::pageMeta('reports'), $buildAdminReportsData($request)));
})->name('reports');

Route::get('/reports/{reportSlug}', function (Request $request, string $reportSlug) use ($buildAdminReportsData, $buildExcelResponse) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    $reportData = $buildAdminReportsData($request);
    $selectedReport = collect($reportData['reportCatalog'])->firstWhere('slug', $reportSlug);
    abort_unless($selectedReport, 404);

    if ($request->boolean('export')) {
        return $buildExcelResponse($selectedReport, $reportData);
    }

    return view('admin.report-detail', array_merge(RouteHelpers::pageMeta('reports'), $reportData, ['selectedReport' => $selectedReport]));
})->name('reports.show');

Route::get('/export/member-data', function () use ($buildMemberExportResponse) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    return $buildMemberExportResponse();
})->name('export.member-data');

Route::post('/reports/expenses', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    ExpenseRecord::create($request->validate([
        'title' => 'required|string|max:255',
        'category' => 'required|string|max:60',
        'amount' => 'required|integer|min:1',
        'payment_method' => 'nullable|in:cash,qris',
        'expense_date' => 'required|date',
        'notes' => 'nullable|string',
    ]));
    return back()->with('status', 'Pengeluaran berhasil ditambahkan.');
})->name('reports.expenses.store');
