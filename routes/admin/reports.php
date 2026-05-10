<?php

use App\Helpers\RouteHelpers;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
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
    $nonMemberRecords   = DailyGuest::query()->get();
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
    $activeDetailTab = in_array($request->query('detail_tab'), ['activity', 'membership', 'member-payments', 'non-member-payments', 'other-transactions', 'expenses'], true) ? $request->query('detail_tab') : 'activity';
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
        'non_member_revenue'        => $monthlyVerifiedTransactions->where('transaction_group', 'daily_pass')->sum('amount'),
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

    // ── REPORT CATALOG (PERBAIKAN ERROR date_label) ───────────────────────────
    $reportCatalog = collect([
        [
            'title'       => 'Aktivitas Latihan',
            'group'       => 'Operasional',
            'summary'     => 'Log riwayat kehadiran member.',
            'date_label'  => $reportDateLabel,
            'count_label' => $checkinRecords->count() . ' aktivitas',
            'highlight'   => $dailyTrainingStats['today_checkins'] . ' hari ini',
            'preview'     => 'Catatan member masuk'
        ],
        [
            'title'       => 'Ringkasan Membership',
            'group'       => 'Operasional',
            'summary'     => 'Status aktif dan expired member.',
            'date_label'  => 'Per ' . now()->format('d M Y'),
            'count_label' => $memberRecords->count() . ' member',
            'highlight'   => $membershipReportSummary['ending'] . ' hampir habis',
            'preview'     => 'Status masa aktif'
        ],
        [
            'title'       => 'Keuangan Bulanan',
            'group'       => 'Keuangan',
            'summary'     => 'Laporan laba rugi bulanan.',
            'date_label'  => $financialSummary['month_label'],
            'count_label' => $financialSummary['verified_transaction_count'] . ' transaksi',
            'highlight'   => 'Rp' . number_format($financialSummary['net_revenue'], 0, ',', '.'),
            'preview'     => 'Pemasukan vs Pengeluaran'
        ],
    ])->map(function ($report) {
        $report['slug'] = Str::slug($report['title']);
        return $report;
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
        'nonMemberPaymentRecords' => collect(),
        'otherTransactionRecords' => collect(),
    ];
};

// ── Routes ────────────────────────────────────────────────────────────────────
Route::get('/reports', function (Request $request) use ($buildAdminReportsData) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    return view('admin.reports', array_merge(RouteHelpers::pageMeta('reports'), $buildAdminReportsData($request)));
})->name('reports');

Route::get('/reports/{reportSlug}', function (Request $request, string $reportSlug) use ($buildAdminReportsData) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    $reportData = $buildAdminReportsData($request);
    $selectedReport = collect($reportData['reportCatalog'])->firstWhere('slug', $reportSlug);
    abort_unless($selectedReport, 404);
    return view('admin.report-detail', array_merge(RouteHelpers::pageMeta('reports'), $reportData, ['selectedReport' => $selectedReport]));
})->name('reports.show');

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
