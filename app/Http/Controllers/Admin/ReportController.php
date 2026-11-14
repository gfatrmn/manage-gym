<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\CashierTransaction;
use App\Models\ExpenseRecord;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $data = $this->buildAdminReportsData($request);

        return Inertia::render('Admin/Reports', array_merge([
            'pageTitle' => 'Laporan - Arena Gym',
            'activePage' => 'reports',
        ], $data));
    }

    public function show(Request $request, string $reportSlug): Response|HttpResponse
    {
        $reportData = $this->buildAdminReportsData($request);
        $selectedReport = collect($reportData['reportCatalog'])->firstWhere('slug', $reportSlug);
        abort_unless($selectedReport, 404);

        if ($request->boolean('export')) {
            return $this->buildExcelResponse($selectedReport, $reportData);
        }

        return Inertia::render('Admin/ReportDetail', array_merge([
            'pageTitle' => ($selectedReport['title'] ?? 'Detail Laporan') . ' - Arena Gym',
            'activePage' => 'reports',
            'selectedReport' => $selectedReport,
        ], $reportData));
    }

    public function exportMemberData(): HttpResponse
    {
        $escape = fn ($value): string => e((string) $value);
        $members = GymMember::query()->where('status', 'member')->orderBy('full_name')->get();
        $filename = 'data-member-' . now()->format('Ymd-His') . '.xls';
        $columns = ['Nama', 'Kode Check-in', 'Status', 'Paket', 'Metode Pembayaran', 'Tanggal Daftar', 'Berakhir', 'Catatan'];

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
                $member->checkin_code ?? '-',
                RouteHelpers::memberLifecycleStatus($member)['label'],
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
    }

    public function storeExpense(Request $request)
    {
        ExpenseRecord::query()->create($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:1'],
            'payment_method' => ['nullable', 'in:cash,qris'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]));

        return back()->with('status', 'Pengeluaran berhasil ditambahkan.');
    }

    private function buildAdminReportsData(Request $request): array
    {
        $today = Carbon::today();
        $memberRecords = GymMember::query()->where('status', 'member')->get();
        $checkinRecords = GymCheckin::query()
            ->with('member')
            ->where('verification_status', 'verified')
            ->latest('checked_in_at')
            ->get();
        $cashierTransactions = CashierTransaction::query()
            ->with(['member', 'product'])
            ->latest('transaction_at')
            ->get();
        $expenseRecords = ExpenseRecord::query()
            ->latest('expense_date')
            ->latest()
            ->get();
        $masterProducts = Product::query()
            ->orderByDesc('is_active')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $verifiedTransactions = $cashierTransactions->filter(fn (CashierTransaction $t) => $t->payment_status === 'verified')->values();
        $memberPaymentRecords = $verifiedTransactions->where('transaction_group', 'member_payment')->values();
        $dailyPassPaymentRecords = $verifiedTransactions->where('transaction_group', 'daily_pass')->values();
        $productSaleRecords = $verifiedTransactions->where('transaction_group', 'product_sale')->values();

        $activeMembers = $memberRecords->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->copy()->startOfDay()->gt($today->copy()->addDays(7)));
        $endingSoonMembers = $memberRecords->filter(fn (GymMember $m) => $m->expires_at && $m->expires_at->copy()->startOfDay()->betweenIncluded($today, $today->copy()->addDays(7)));
        $expiredMembers = $memberRecords->filter(fn (GymMember $m) => ! $m->expires_at || $m->expires_at->copy()->startOfDay()->lt($today));

        $membershipReportSummary = [
            'active' => $activeMembers->count(),
            'ending' => $endingSoonMembers->count(),
            'expired' => $expiredMembers->count(),
        ];

        $detailFilterType = in_array($request->query('detail_filter'), ['month', 'range'], true) ? $request->query('detail_filter') : 'month';
        $detailMonthInput = (string) $request->query('detail_month', now()->format('Y-m'));
        $detailMonth = preg_match('/^\d{4}-\d{2}$/', $detailMonthInput)
            ? Carbon::createFromFormat('Y-m', $detailMonthInput)->startOfMonth()
            : now()->startOfMonth();

        $detailFrom = $request->filled('detail_from') ? Carbon::parse($request->query('detail_from'))->startOfDay() : $detailMonth->copy()->startOfMonth();
        $detailTo = $request->filled('detail_to') ? Carbon::parse($request->query('detail_to'))->endOfDay() : $detailMonth->copy()->endOfMonth();

        if ($detailTo->lt($detailFrom)) {
            [$detailFrom, $detailTo] = [$detailTo->copy()->startOfDay(), $detailFrom->copy()->endOfDay()];
        }

        $detailRangeStart = $detailFilterType === 'range' ? $detailFrom->copy()->startOfDay() : $detailMonth->copy()->startOfMonth();
        $detailRangeEnd = $detailFilterType === 'range' ? $detailTo->copy()->endOfDay() : $detailMonth->copy()->endOfMonth();
        $reportDateLabel = $detailFilterType === 'range' ? $detailFrom->format('d M Y') . ' - ' . $detailTo->format('d M Y') : $detailMonth->translatedFormat('F Y');

        $reportMonthStart = $detailMonth->copy()->startOfMonth();
        $reportMonthEnd = $detailMonth->copy()->endOfMonth();

        $monthlyVerifiedTransactions = $verifiedTransactions->filter(fn ($t) => $t->transaction_at && $t->transaction_at->betweenIncluded($reportMonthStart, $reportMonthEnd))->values();
        $monthlyExpenseRecords = $expenseRecords->filter(fn ($e) => $e->expense_date && $e->expense_date->betweenIncluded($reportMonthStart, $reportMonthEnd))->values();

        $totalRev = (int) $monthlyVerifiedTransactions->sum('amount');
        $totalExp = (int) $monthlyExpenseRecords->sum('amount');

        $financialSummary = [
            'month_label' => $reportMonthStart->translatedFormat('F Y'),
            'total_revenue' => $totalRev,
            'total_expense' => $totalExp,
            'net_revenue' => $totalRev - $totalExp,
            'member_revenue' => (int) $monthlyVerifiedTransactions->where('transaction_group', 'member_payment')->sum('amount'),
            'daily_pass_revenue' => (int) $monthlyVerifiedTransactions->where('transaction_group', 'daily_pass')->sum('amount'),
            'product_revenue' => (int) $monthlyVerifiedTransactions->where('transaction_group', 'product_sale')->sum('amount'),
            'other_revenue' => (int) $monthlyVerifiedTransactions->where('transaction_group', 'other')->sum('amount'),
            'verified_transaction_count' => $monthlyVerifiedTransactions->count(),
            'expense_count' => $monthlyExpenseRecords->count(),
        ];

        $financialMonthlyHistory = collect(range(0, 5))->map(function (int $offset) use ($verifiedTransactions, $expenseRecords) {
            $monthStart = now()->copy()->subMonths($offset)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $mRev = (int) $verifiedTransactions->filter(fn ($t) => $t->transaction_at && $t->transaction_at->betweenIncluded($monthStart, $monthEnd))->sum('amount');
            $mExp = (int) $expenseRecords->filter(fn ($e) => $e->expense_date && $e->expense_date->betweenIncluded($monthStart, $monthEnd))->sum('amount');

            return [
                'month_label' => $monthStart->translatedFormat('F Y'),
                'short_label' => $monthStart->translatedFormat('M y'),
                'total_revenue' => $mRev,
                'total_expense' => $mExp,
                'net_revenue' => $mRev - $mExp,
            ];
        })->values()->reverse()->values();

        $buildDailyRows = function (Carbon $start, Carbon $end, callable $callback): array {
            $rows = [];
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $rows[] = $callback($date);
            }

            return $rows;
        };

        $buildMonthlyRows = function (int $year, callable $callback): array {
            $rows = [];
            for ($month = 1; $month <= 12; $month++) {
                $rows[] = $callback(Carbon::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $year, $month)));
            }

            return $rows;
        };

        $formatMoney = fn (int $amount): string => RouteHelpers::formatCurrency($amount);
        $todayCheckinsCount = $checkinRecords->filter(fn ($c) => $c->checked_in_at?->isToday())->count();

        $stockSales = $productSaleRecords->filter(fn ($t) => $t->transaction_at && $t->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd));
        $stockSalesByProduct = $stockSales->groupBy('product_id')->map(fn ($items) => [
            'quantity' => (int) $items->sum('quantity'),
            'amount' => (int) $items->sum('amount'),
        ]);

        $rangeTransactions = $verifiedTransactions->filter(fn ($t) => $t->transaction_at && $t->transaction_at->betweenIncluded($detailRangeStart, $detailRangeEnd))->values();
        $rangeExpenses = $expenseRecords->filter(fn ($e) => $e->expense_date && $e->expense_date->betweenIncluded($detailRangeStart, $detailRangeEnd))->values();

        $reportCatalog = collect([
            [
                'title' => 'Laporan Member',
                'slug' => 'laporan-member',
                'group' => 'Operasional',
                'summary' => 'Ringkasan keanggotaan member, status aktif, member baru, dan expired.',
                'date_label' => 'Per ' . now()->format('d M Y'),
                'count_label' => $memberRecords->count() . ' member',
                'highlight' => $membershipReportSummary['ending'] . ' perlu follow-up',
                'preview' => 'Data membership lengkap',
                'details' => ['Jumlah member aktif', 'Member baru', 'Member expired', 'Riwayat pendaftaran'],
                'daily_stat_columns' => ['Tanggal', 'Member Aktif', 'Member Baru', 'Expired'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) use ($memberRecords) {
                    $activeOnDate = $memberRecords->filter(fn ($m) => $m->joined_at && $m->joined_at->lte($date) && (! $m->expires_at || $m->expires_at->gte($date)));
                    $newOnDate = $memberRecords->filter(fn ($m) => $m->joined_at && $m->joined_at->equalTo($date));
                    $expiredOnDate = $memberRecords->filter(fn ($m) => $m->expires_at && $m->expires_at->equalTo($date));

                    return [$date->translatedFormat('d M'), $activeOnDate->count(), $newOnDate->count(), $expiredOnDate->count()];
                }),
                'monthly_stat_columns' => ['Bulan', 'Member Aktif', 'Member Baru', 'Expired'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) use ($memberRecords) {
                    $ms = $date->copy()->startOfMonth();
                    $me = $date->copy()->endOfMonth();
                    $active = $memberRecords->filter(fn ($m) => $m->joined_at && $m->joined_at->lte($me) && (! $m->expires_at || $m->expires_at->gte($ms)))->count();
                    $new = $memberRecords->filter(fn ($m) => $m->joined_at && $m->joined_at->betweenIncluded($ms, $me))->count();
                    $exp = $memberRecords->filter(fn ($m) => $m->expires_at && $m->expires_at->betweenIncluded($ms, $me))->count();

                    return [$date->translatedFormat('M'), $active, $new, $exp];
                }),
                'columns' => ['Nama Member', 'Kode Check-in', 'Status', 'Berakhir', 'Tanggal Daftar', 'Metode Pembayaran'],
                'rows' => $memberRecords->map(fn ($member) => [
                    $member->full_name,
                    $member->checkin_code ?? '-',
                    RouteHelpers::memberLifecycleStatus($member)['label'],
                    $member->expires_at?->format('d M Y') ?? '-',
                    $member->joined_at?->format('d M Y') ?? '-',
                    $member->payment_method ? strtoupper($member->payment_method) : '-',
                ])->values()->toArray(),
                'member_table_columns' => ['Nama Member', 'Metode Pembayaran', 'Tanggal Daftar', 'Berakhir'],
                'member_table_groups' => [
                    'Aktif' => $memberRecords->filter(fn ($m) => $m->expires_at && $m->expires_at->gte($today))->values(),
                    'Baru' => $memberRecords->filter(fn ($m) => $m->joined_at && $m->joined_at->betweenIncluded($reportMonthStart, $reportMonthEnd))->values(),
                    'Expired' => $memberRecords->filter(fn ($m) => $m->expires_at && $m->expires_at->betweenIncluded($reportMonthStart, $reportMonthEnd) && $m->expires_at->lt($today))->values(),
                ],
                'reportSections' => [
                    'Daftar Member Aktif' => [
                        'columns' => ['Nama Member', 'Kode', 'Metode', 'Daftar', 'Berakhir'],
                        'rows' => $memberRecords->filter(fn ($m) => $m->expires_at && $m->expires_at->gte($today))->map(fn ($m) => [
                            $m->full_name,
                            $m->checkin_code ?? '-',
                            strtoupper((string) ($m->payment_method ?? '-')),
                            $m->joined_at?->format('d M Y') ?? '-',
                            $m->expires_at?->format('d M Y') ?? '-',
                        ])->values()->toArray(),
                    ],
                ],
            ],
            [
                'title' => 'Laporan Keuangan',
                'slug' => 'laporan-keuangan',
                'group' => 'Keuangan',
                'summary' => 'Ringkasan pemasukan aktual dari transaksi terverifikasi dan pengeluaran operasional.',
                'date_label' => $financialSummary['month_label'],
                'count_label' => $financialSummary['verified_transaction_count'] . ' transaksi',
                'highlight' => $formatMoney($financialSummary['net_revenue']),
                'preview' => 'Pendapatan, pengeluaran, dan laba bersih',
                'details' => ['Total pemasukan', 'Pembayaran member', 'Riwayat pemasukan', 'Riwayat pengeluaran'],
                'daily_stat_columns' => ['Tanggal', 'Total Pemasukan', 'Pembayaran Member', 'Jumlah Transaksi'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) use ($rangeTransactions, $formatMoney) {
                    $dayItems = $rangeTransactions->filter(fn ($t) => $t->transaction_at && $t->transaction_at->isSameDay($date));
                    $dayMember = (int) $dayItems->where('transaction_group', 'member_payment')->sum('amount');

                    return [
                        $date->translatedFormat('d M'),
                        $formatMoney((int) $dayItems->sum('amount')),
                        $formatMoney($dayMember),
                        $dayItems->count() . ' transaksi',
                    ];
                }),
                'monthly_stat_columns' => ['Bulan', 'Total Pemasukan', 'Total Pengeluaran', 'Laba Bersih'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) use ($verifiedTransactions, $expenseRecords, $formatMoney) {
                    $ms = $date->copy()->startOfMonth();
                    $me = $date->copy()->endOfMonth();
                    $inc = (int) $verifiedTransactions->filter(fn ($t) => $t->transaction_at && $t->transaction_at->betweenIncluded($ms, $me))->sum('amount');
                    $exp = (int) $expenseRecords->filter(fn ($e) => $e->expense_date && $e->expense_date->betweenIncluded($ms, $me))->sum('amount');

                    return [$date->translatedFormat('M'), $formatMoney($inc), $formatMoney($exp), $formatMoney($inc - $exp)];
                }),
                'columns' => ['Tanggal', 'Invoice', 'Sumber', 'Keterangan', 'Jumlah', 'Metode'],
                'rows' => $rangeTransactions->map(fn ($t) => [
                    $t->transaction_at?->translatedFormat('d M Y, H:i') ?? '-',
                    $t->invoice ?: '-',
                    ucfirst(str_replace('_', ' ', (string) $t->transaction_group)),
                    $t->customer_name ?? $t->member?->full_name ?? '-',
                    $formatMoney((int) $t->amount),
                    strtoupper((string) ($t->payment_method ?? '-')),
                ])->toArray(),
                'reportSections' => [
                    'Sumber Pendapatan' => [
                        'columns' => ['Sumber', 'Jumlah', 'Catatan'],
                        'rows' => [
                            ['Pembayaran member', $formatMoney((int) $rangeTransactions->where('transaction_group', 'member_payment')->sum('amount')), 'Pembayaran paket dan perpanjangan'],
                            ['Pembayaran daily pass', $formatMoney((int) $rangeTransactions->where('transaction_group', 'daily_pass')->sum('amount')), 'Daily pass dan pengunjung harian'],
                            ['Penjualan produk', $formatMoney((int) $rangeTransactions->where('transaction_group', 'product_sale')->sum('amount')), 'Suplemen, vitamin, dan minuman'],
                            ['Transaksi lainnya', $formatMoney((int) $rangeTransactions->where('transaction_group', 'other')->sum('amount')), 'Sewa loker dan layanan lain'],
                        ],
                    ],
                    'Riwayat Pemasukan' => [
                        'columns' => ['Tanggal', 'Invoice', 'Sumber', 'Keterangan', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $rangeTransactions->sortByDesc('transaction_at')->map(fn ($t) => [
                            $t->transaction_at?->format('d M Y H:i') ?? '-',
                            $t->invoice ?: '-',
                            ucfirst(str_replace('_', ' ', (string) $t->transaction_group)),
                            $t->customer_name ?? $t->member?->full_name ?? 'Tidak dikenal',
                            $formatMoney((int) $t->amount),
                            strtoupper((string) ($t->payment_method ?? 'CASH')),
                        ])->values()->toArray(),
                    ],
                    'Riwayat Pengeluaran' => [
                        'columns' => ['Tanggal', 'Kategori', 'Keterangan', 'Jumlah', 'Metode Pembayaran'],
                        'rows' => $rangeExpenses->sortByDesc('expense_date')->map(fn ($expense) => [
                            $expense->expense_date?->format('d M Y') ?? '-',
                            ucfirst(str_replace('_', ' ', (string) ($expense->category ?? 'pengeluaran'))),
                            $expense->title,
                            $formatMoney((int) $expense->amount),
                            strtoupper((string) ($expense->payment_method ?? 'CASH')),
                        ])->values()->toArray(),
                    ],
                ],
            ],
            [
                'title' => 'Laporan Kehadiran',
                'slug' => 'laporan-kehadiran',
                'group' => 'Operasional',
                'summary' => 'Rekap check-in member untuk analisis kehadiran harian dan bulanan.',
                'date_label' => $reportDateLabel,
                'count_label' => $checkinRecords->count() . ' check-in',
                'highlight' => $todayCheckinsCount . ' check-in hari ini',
                'preview' => 'Jam kehadiran member',
                'details' => ['Jam check-in', 'Jumlah hadir per hari', 'Member hadir', 'Status kehadiran'],
                'daily_stat_columns' => ['Tanggal', 'Jumlah Check-in', 'Member Hadir', 'Check-in Terakhir'],
                'daily_stat_rows' => $buildDailyRows($detailRangeStart, $detailRangeEnd, function (Carbon $date) use ($checkinRecords) {
                    $dailyCheckins = $checkinRecords->filter(fn ($c) => $c->checked_in_at && $c->checked_in_at->isSameDay($date));
                    $names = $dailyCheckins->map(fn ($c) => $c->member?->full_name ?? $c->submitted_name ?? 'Tidak dikenal')->unique()->values();

                    return [
                        $date->translatedFormat('d M'),
                        $dailyCheckins->count(),
                        $names->take(3)->implode(', ') . ($names->count() > 3 ? ' +' . ($names->count() - 3) . ' lain' : ''),
                        $dailyCheckins->sortByDesc('checked_in_at')->first()?->checked_in_at?->format('H:i') ?? '-',
                    ];
                }),
                'monthly_stat_columns' => ['Bulan', 'Jumlah Check-in', 'Member Unik', 'Rata-rata per Hari'],
                'monthly_stat_rows' => $buildMonthlyRows($detailMonth->year, function (Carbon $date) use ($checkinRecords) {
                    $ms = $date->copy()->startOfMonth();
                    $me = $date->copy()->endOfMonth();
                    $monthlyCheckins = $checkinRecords->filter(fn ($c) => $c->checked_in_at && $c->checked_in_at->betweenIncluded($ms, $me));
                    $uniqueMembers = $monthlyCheckins->map(fn ($c) => $c->gym_member_id ?: ($c->submitted_name . '|' . $c->submitted_phone))->filter()->unique()->count();

                    return [
                        $date->translatedFormat('M'),
                        $monthlyCheckins->count(),
                        $uniqueMembers,
                        number_format($monthlyCheckins->count() / max($date->daysInMonth, 1), 1, ',', '.'),
                    ];
                }),
                'columns' => ['Tanggal', 'Nama Member', 'No. HP', 'Waktu Check-in', 'Metode', 'Status', 'Catatan'],
                'rows' => $checkinRecords
                    ->filter(fn ($c) => $c->checked_in_at && $c->checked_in_at->betweenIncluded($detailRangeStart, $detailRangeEnd))
                    ->sortByDesc('checked_in_at')
                    ->map(fn ($c) => [
                        $c->checked_in_at?->translatedFormat('d M Y') ?? '-',
                        $c->member?->full_name ?? $c->submitted_name ?? 'Tidak dikenal',
                        $c->member?->phone ?? $c->submitted_phone ?? '-',
                        $c->checked_in_at?->format('H:i') ?? '-',
                        ucfirst(str_replace('_', ' ', (string) ($c->checkin_method ?? 'admin'))),
                        ucfirst((string) ($c->verification_status ?? 'verified')),
                        $c->notes ?: '-',
                    ])->values()->toArray(),
            ],
            [
                'title' => 'Laporan Stok Barang',
                'slug' => 'laporan-stok-barang',
                'group' => 'Operasional',
                'summary' => 'Daftar stok produk suplemen & vitamin serta rincian barang terjual.',
                'date_label' => 'Per ' . now()->format('d M Y'),
                'count_label' => $masterProducts->count() . ' produk',
                'highlight' => $masterProducts->where('stock', '<', 5)->count() . ' stok menipis',
                'preview' => 'Stok produk & penjualan',
                'details' => ['Nama produk', 'Brand dan SKU', 'Stok saat ini', 'Harga jual', 'Barang terjual'],
                'daily_stat_columns' => ['Tanggal', 'Total Produk', 'Total Stok', 'Stok Rendah (<5)', 'Terjual'],
                'daily_stat_rows' => [[
                    now()->translatedFormat('d M'),
                    $masterProducts->count(),
                    $masterProducts->sum('stock'),
                    $masterProducts->where('stock', '<', 5)->count(),
                    $stockSales->sum('quantity'),
                ]],
                'monthly_stat_columns' => ['Bulan', 'Total Produk', 'Total Stok', 'Stok Rendah (<5)', 'Terjual'],
                'monthly_stat_rows' => [[
                    $detailMonth->translatedFormat('M'),
                    $masterProducts->count(),
                    $masterProducts->sum('stock'),
                    $masterProducts->where('stock', '<', 5)->count(),
                    $stockSales->sum('quantity'),
                ]],
                'columns' => ['Nama Produk', 'Kategori', 'Brand', 'SKU', 'Stok', 'Terjual', 'Pendapatan', 'Status'],
                'rows' => $masterProducts->map(fn ($product) => [
                    $product->name,
                    ucfirst((string) $product->category),
                    $product->brand ?: '-',
                    $product->sku ?: '-',
                    number_format($product->stock, 0, ',', '.') . ' ' . $product->unit,
                    $stockSalesByProduct->get($product->id)['quantity'] ?? 0,
                    $formatMoney($stockSalesByProduct->get($product->id)['amount'] ?? 0),
                    $product->is_active ? 'Aktif' : 'Nonaktif',
                ])->toArray(),
                'detail_title' => 'Daftar Stok Produk',
                'reportSections' => [
                    'Rincian Penjualan' => [
                        'columns' => ['Produk', 'Jumlah Terjual', 'Pendapatan'],
                        'rows' => $masterProducts->map(fn ($product) => [
                            $product->name,
                            $stockSalesByProduct->get($product->id)['quantity'] ?? 0,
                            $formatMoney($stockSalesByProduct->get($product->id)['amount'] ?? 0),
                        ])->toArray(),
                    ],
                    'Rincian Barang Terjual' => [
                        'columns' => ['Tanggal', 'Invoice', 'Pelanggan', 'Produk', 'Brand', 'SKU', 'Qty', 'Harga Satuan', 'Total', 'Pembayaran', 'Status'],
                        'rows' => $stockSales->sortByDesc('transaction_at')->map(fn ($transaction) => [
                            $transaction->transaction_at?->translatedFormat('d M Y H:i') ?? '-',
                            $transaction->invoice ?: '-',
                            $transaction->customer_name ?: ($transaction->member?->full_name ?? 'Daily Pass'),
                            $transaction->product?->name ?? $transaction->transaction_type ?? 'Produk tidak dikenal',
                            $transaction->product?->brand ?: '-',
                            $transaction->product?->sku ?: '-',
                            number_format((int) ($transaction->quantity ?? 0), 0, ',', '.') . ' ' . ($transaction->product?->unit ?? 'pcs'),
                            $formatMoney((int) round(($transaction->amount ?? 0) / max((int) ($transaction->quantity ?? 1), 1))),
                            $formatMoney((int) ($transaction->amount ?? 0)),
                            strtoupper((string) ($transaction->payment_method ?? 'CASH')),
                            ucfirst((string) ($transaction->payment_status ?? '-')),
                        ])->values()->toArray(),
                    ],
                ],
            ],
        ])->values()->all();

        return [
            'financialSummary' => $financialSummary,
            'financialMonthlyHistory' => $financialMonthlyHistory,
            'dailyTrainingStats' => ['today_checkins' => $todayCheckinsCount],
            'membershipReportSummary' => $membershipReportSummary,
            'reportCatalog' => $reportCatalog,
            'detailFilterType' => $detailFilterType,
            'detailFilterMonth' => $detailMonth->format('Y-m'),
            'detailFrom' => $detailFrom->format('Y-m-d'),
            'detailTo' => $detailTo->format('Y-m-d'),
            'detailFilterLabel' => $reportDateLabel,
        ];
    }

    private function buildExcelResponse(array $selectedReport, array $reportData): HttpResponse
    {
        $escape = fn ($value): string => e((string) $value);

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

        if (! empty($selectedReport['reportSections'])) {
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
    }
}

