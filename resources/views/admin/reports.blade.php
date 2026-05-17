@extends('admin.layout')

@section('content')
    <style>
        .reports-page .table th,
        .reports-page .table td,
        .report-detail-table th,
        .report-detail-table td {
            vertical-align: middle;
        }

        .reports-page .form-control,
        .reports-page .form-select,
        .reports-page .btn {
            min-height: 44px;
        }

        .reports-filter-card,
        .reports-list-card,
        .reports-expense-card,
        .reports-finance-card {
            border: 1px solid var(--border);
            border-radius: 1rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .reports-page .topbar-card,
        .reports-page .metric-card {
            border-radius: 1rem;
        }

        .reports-page .metric-card {
            min-height: 0;
        }

        .reports-page .metric-card .fs-2 {
            line-height: 1.15;
            word-break: break-word;
        }

        .reports-stat-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
            font-size: .85rem;
            font-weight: 600;
        }

        .reports-stat-badge strong {
            color: var(--text-main);
            white-space: nowrap;
        }

        .reports-category-badge {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.12);
            color: var(--accent, #0d6efd);
            font-size: .78rem;
            font-weight: 700;
        }

        .reports-list-card .table thead th {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
            border-bottom-color: var(--border);
            background: rgba(255, 255, 255, 0.03);
            white-space: nowrap;
        }

        .reports-list-card .table td {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .reports-list-card .table tbody tr + tr td {
            border-top-color: var(--border);
        }

        .reports-list-card .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .reports-report-title {
            color: var(--text-main);
            font-size: 1rem;
        }

        .reports-highlight {
            min-width: 130px;
        }

        .reports-action-btn {
            min-width: 118px;
            font-weight: 700;
        }

        .reports-finance-table td:last-child,
        .reports-finance-table th:last-child {
            text-align: right;
        }

        .reports-finance-table tbody tr:last-child td {
            border-top: 1px solid var(--border);
            font-weight: 800;
        }

        .reports-month-form .form-label {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        @media (max-width: 575.98px) {
            .reports-page .topbar-card,
            .reports-filter-card,
            .reports-expense-card,
            .reports-finance-card {
                padding: 1rem !important;
            }

            .reports-action-btn {
                width: 100%;
            }
        }
    </style>

    <div class="reports-page">
        <div class="topbar-card p-4 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-xl-7">
                    <div class="section-label">Laporan</div>
                    <h1 class="h2 fw-bold mt-2 mb-0">Pusat laporan Arena Gym</h1>
                </div>
                <div class="col-12 col-xl-5">
                    <div class="row g-3">
                        <div class="col-6 col-lg-4">
                            <div class="reports-stat-badge w-100 justify-content-between">
                                <span>Laporan</span>
                                <strong>{{ $reportCatalog->count() }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-lg-4">
                            <div class="reports-stat-badge w-100 justify-content-between">
                                <span>Periode</span>
                                <strong>{{ $financialSummary['month_label'] }}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="reports-stat-badge w-100 justify-content-between">
                                <span>Transaksi</span>
                                <strong>{{ $financialSummary['verified_transaction_count'] }} / {{ $financialSummary['expense_count'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Check-in Hari Ini</div>
                    <div class="fs-2 fw-bold mt-2">{{ $dailyTrainingStats['today_checkins'] }}</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Member Perlu Follow-up</div>
                    <div class="fs-2 fw-bold mt-2">{{ $membershipReportSummary['ending'] + $membershipReportSummary['expired'] }}</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Pemasukan Bulan Ini</div>
                    <div class="fs-2 fw-bold mt-2">Rp{{ number_format($financialSummary['total_revenue'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Laba Bersih</div>
                    <div class="fs-2 fw-bold mt-2">Rp{{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="card reports-filter-card p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-xl-5">
                    <div class="section-label">Daftar Laporan</div>
                    <h2 class="h4 fw-bold mt-2 mb-0">Pilih laporan</h2>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <form method="GET" action="{{ route('admin.reports') }}" class="reports-month-form">
                        <input type="hidden" name="detail_filter" value="month">
                        <label class="form-label fw-semibold small mb-2" for="report_month_filter">Filter bulan laporan</label>
                        <div class="input-group">
                            <input id="report_month_filter" type="month" name="detail_month" class="form-control" value="{{ $detailFilterMonth }}">
                            <button type="submit" class="btn btn-dark px-4 fw-semibold">Terapkan</button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <label class="form-label fw-semibold small mb-2">Cari laporan</label>
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Cari: keuangan, member, stok" data-report-search>
                    </div>
                </div>
            </div>
        </div>

        <div class="card reports-list-card p-0 mb-4 overflow-hidden">
            <div class="card-header bg-transparent border-0 px-4 pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <div class="reports-stat-badge" data-report-search-summary>Menampilkan {{ $reportCatalog->count() }} dari {{ $reportCatalog->count() }} laporan.</div>
                    <div class="reports-stat-badge">Periode: {{ $detailFilterLabel }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Jenis Laporan</th>
                            <th>Kategori</th>
                            <th>Tanggal Laporan</th>
                            <th>Data</th>
                            <th>Highlight</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportCatalog as $report)
                            <tr data-report-row data-report-search-text="{{ strtolower($report['title'].' '.$report['group'].' '.$report['summary']) }}">
                                <td class="ps-4">
                                    <div class="fw-semibold reports-report-title">{{ $report['title'] }}</div>
                                </td>
                                <td>
                                    <span class="reports-category-badge">{{ $report['group'] }}</span>
                                </td>
                                <td class="small fw-semibold">{{ $report['date_label'] }}</td>
                                <td class="fw-semibold">{{ $report['count_label'] }}</td>
                                <td class="fw-semibold reports-highlight">{{ $report['highlight'] }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.reports.show', ['reportSlug' => $report['slug'], 'detail_filter' => 'month', 'detail_month' => $detailFilterMonth]) }}" class="btn btn-dark rounded-pill px-4 reports-action-btn">Lihat Detail</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="d-none" data-report-empty-search>
                            <td colspan="6" class="text-center py-5 text-secondary">Jenis laporan tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <div class="card reports-expense-card h-100">
                    <div class="card-body p-4">
                        <div class="section-label">Pengeluaran</div>
                        <h3 class="h5 fw-bold mt-2 mb-1">Tambah pengeluaran</h3>

                        <form method="POST" action="{{ route('admin.reports.expenses.store') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="expense_title">Nama Pengeluaran</label>
                                    <input id="expense_title" type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Bayar listrik bulanan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="expense_category">Kategori</label>
                                    <select id="expense_category" name="category" class="form-select" required>
                                        <option value="">Pilih kategori</option>
                                        <option value="operasional" @selected(old('category') === 'operasional')>Operasional</option>
                                        <option value="stok_barang" @selected(old('category') === 'stok_barang')>Stok Barang</option>
                                        <option value="maintenance" @selected(old('category') === 'maintenance')>Maintenance</option>
                                        <option value="gaji" @selected(old('category') === 'gaji')>Gaji</option>
                                        <option value="lainnya" @selected(old('category') === 'lainnya')>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="expense_amount">Nominal</label>
                                    <input id="expense_amount" type="number" min="1" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="150000" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="expense_method">Metode Bayar</label>
                                    <select id="expense_method" name="payment_method" class="form-select">
                                        <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                                        <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="expense_date">Tanggal</label>
                                    <input id="expense_date" type="date" name="expense_date" class="form-control" value="{{ old('expense_date', now()->toDateString()) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="expense_notes">Catatan</label>
                                    <input id="expense_notes" type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Opsional, misalnya detail vendor atau kebutuhan follow-up">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-dark rounded-pill w-100 fw-semibold">Simpan Pengeluaran</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="card reports-finance-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
                            <div>
                                <div class="section-label">Ringkasan Keuangan</div>
                                <h3 class="h5 fw-bold mt-2 mb-1">Angka utama bulan berjalan</h3>
                            </div>
                            <span class="reports-stat-badge">{{ $financialSummary['month_label'] }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0 reports-finance-table">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pemasukan member</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['member_revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pemasukan non-member</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['non_member_revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Transaksi lain</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['other_revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total pemasukan</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['total_revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total pengeluaran</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['total_expense'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Laba bersih</td>
                                        <td class="fw-semibold">Rp{{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('[data-report-search]');
            const rows = document.querySelectorAll('[data-report-row]');
            const summary = document.querySelector('[data-report-search-summary]');
            const emptyRow = document.querySelector('[data-report-empty-search]');

            const applySearch = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    const haystack = (row.dataset.reportSearchText || '').toLowerCase();
                    const matched = keyword === '' || haystack.includes(keyword);
                    row.classList.toggle('d-none', !matched);

                    if (matched) {
                        visibleCount += 1;
                    }
                });

                if (summary) {
                    summary.textContent = `Menampilkan ${visibleCount} dari ${rows.length} laporan.`;
                }

                emptyRow?.classList.toggle('d-none', visibleCount !== 0);
            };

            searchInput?.addEventListener('input', applySearch);
            applySearch();
        });
    </script>
@endsection
