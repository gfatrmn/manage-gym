@extends('admin.layout')

@section('content')
    <style>
        .reports-page .summary-card,
        .reports-page .filter-card,
        .reports-page .report-card,
        .reports-page .finance-card,
        .reports-page .expense-card {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .reports-page .summary-card {
            min-height: 132px;
        }

        .reports-page .summary-icon {
            width: 2.85rem;
            height: 2.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(255, 59, 59, .12);
            color: #ff4b53;
            overflow: hidden;
            padding: .25rem;
        }

        .reports-page .summary-icon svg,
        .reports-page .report-icon svg {
            width: 1.45rem;
            height: 1.45rem;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .reports-page .summary-value {
            color: var(--text-main);
            font-size: clamp(1.7rem, 2.5vw, 2.35rem);
            line-height: 1.08;
            font-weight: 800;
            word-break: break-word;
        }

        .reports-page .help-text {
            color: var(--text-muted);
            font-size: .92rem;
            line-height: 1.55;
        }

        .reports-page .report-card {
            transition: transform .18s ease, border-color .18s ease, background .18s ease;
        }

        .reports-page .report-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 59, 59, .38);
            background: rgba(255, 255, 255, .045);
        }

        .reports-page .report-icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: rgba(255, 59, 59, .12);
            color: #ff4b53;
            flex: 0 0 auto;
            overflow: hidden;
            padding: .25rem;
        }

        .reports-page .report-meta {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .78rem;
            font-weight: 700;
        }

        .reports-page .finance-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .9rem 0;
            border-bottom: 1px solid var(--border);
        }

        .reports-page .finance-row:last-child {
            border-bottom: 0;
            font-weight: 800;
        }

        .reports-page .form-control,
        .reports-page .btn {
            min-height: 44px;
        }

        @media (max-width: 575.98px) {
            .reports-page .filter-card,
            .reports-page .finance-card,
            .reports-page .expense-card,
            .reports-page .report-card,
            .reports-page .summary-card {
                padding: 1rem !important;
            }
        }
    </style>

    @php
        $detailRouteQuery = [
            'detail_filter' => 'month',
            'detail_month' => $detailFilterMonth,
        ];

        $reportIcons = [
            'laporan-member' => 'members',
            'laporan-keuangan' => 'money',
            'laporan-kehadiran' => 'attendance',
            'laporan-stok-barang' => 'stock',
        ];

        $iconSvg = function (string $name): string {
            $icons = [
                'checkin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 11l2 2 4-5"/><path d="M20 12a8 8 0 1 1-4.7-7.3"/><path d="M17 3h4v4"/></svg>',
                'followup' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>',
                'income' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18v10H3z"/><path d="M7 7a4 4 0 0 1-4 4"/><path d="M17 7a4 4 0 0 0 4 4"/><path d="M7 17a4 4 0 0 0-4-4"/><path d="M17 17a4 4 0 0 1 4-4"/><circle cx="12" cy="12" r="2"/></svg>',
                'net' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 15l4-4 3 3 5-7"/><path d="M15 7h4v4"/></svg>',
                'members' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'money' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'attendance' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 11l2 2 4-4"/><path d="M21 12a9 9 0 1 1-9-9"/><path d="M21 3l-9 9"/></svg>',
                'stock' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7L12 12l8.7-5"/><path d="M12 22V12"/></svg>',
                'wallet' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7H5a2 2 0 0 1 0-4h13"/><path d="M5 7h16v14H5a2 2 0 0 1-2-2V5"/><path d="M16 14h.01"/></svg>',
                'expense' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>',
            ];

            return $icons[$name] ?? $icons['stock'];
        };
    @endphp

    <div class="reports-page">
        <div class="topbar-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-xl-8">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="brand-logo">
                        <div>
                            <div class="section-label">Laporan Admin</div>
                            <h1 class="h2 fw-bold mt-2 mb-2">Ringkasan laporan gym</h1>
                            <p class="help-text mb-0">Pilih bulan, lalu buka jenis laporan yang ingin dilihat. Halaman ini dibuat untuk membaca kondisi member, uang masuk, kehadiran, dan stok tanpa filter yang rumit.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="filter-card p-3">
                        <form method="GET" action="{{ route('admin.reports') }}">
                            <input type="hidden" name="detail_filter" value="month">
                            <label class="form-label fw-semibold small mb-2" for="report_month_filter">Bulan laporan</label>
                            <div class="input-group">
                                <input id="report_month_filter" type="month" name="detail_month" class="form-control" value="{{ $detailFilterMonth }}">
                                <button type="submit" class="btn btn-dark fw-semibold px-4">Tampilkan</button>
                            </div>
                            <div class="help-text mt-2 small">Periode aktif: {{ $detailFilterLabel }}</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="summary-card p-4 h-100">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="section-label">Check-in hari ini</div>
                            <div class="summary-value mt-2">{{ $dailyTrainingStats['today_checkins'] }}</div>
                            <div class="help-text mt-2">Member yang datang hari ini.</div>
                        </div>
                        <span class="summary-icon">{!! $iconSvg('checkin') !!}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="summary-card p-4 h-100">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="section-label">Perlu dihubungi</div>
                            <div class="summary-value mt-2">{{ $membershipReportSummary['ending'] + $membershipReportSummary['expired'] }}</div>
                            <div class="help-text mt-2">Member hampir habis atau sudah expired.</div>
                        </div>
                        <span class="summary-icon">{!! $iconSvg('followup') !!}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="summary-card p-4 h-100">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="section-label">Uang masuk</div>
                            <div class="summary-value mt-2">Rp{{ number_format($financialSummary['total_revenue'], 0, ',', '.') }}</div>
                            <div class="help-text mt-2">Total pemasukan bulan ini.</div>
                        </div>
                        <span class="summary-icon">{!! $iconSvg('income') !!}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="summary-card p-4 h-100">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="section-label">Sisa bersih</div>
                            <div class="summary-value mt-2">Rp{{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</div>
                            <div class="help-text mt-2">Pemasukan dikurangi pengeluaran.</div>
                        </div>
                        <span class="summary-icon">{!! $iconSvg('net') !!}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-3">
                    <div>
                        <div class="section-label">Buka Laporan</div>
                        <h2 class="h4 fw-bold mt-2 mb-0">Pilih data yang ingin dicek</h2>
                    </div>
                    <span class="report-meta">{{ $reportCatalog->count() }} jenis laporan</span>
                </div>

                <div class="row g-3">
                    @foreach ($reportCatalog as $report)
                        <div class="col-12 col-md-6">
                            <a href="{{ route('admin.reports.show', array_merge(['reportSlug' => $report['slug']], $detailRouteQuery)) }}" class="report-card d-block p-4 h-100 text-decoration-none text-reset">
                                <div class="d-flex gap-3 align-items-start">
                                    <span class="report-icon">{!! $iconSvg($reportIcons[$report['slug']] ?? 'stock') !!}</span>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <h3 class="h5 fw-bold mb-0">{{ $report['title'] }}</h3>
                                            <span class="fs-4 text-danger" aria-hidden="true">&rarr;</span>
                                        </div>
                                        <p class="help-text mb-3">{{ $report['summary'] }}</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="report-meta">{{ $report['count_label'] }}</span>
                                            <span class="report-meta">{{ $report['highlight'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="finance-card p-4 mb-4">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <div class="section-label">Keuangan Bulan Ini</div>
                            <h2 class="h5 fw-bold mt-2 mb-1">{{ $financialSummary['month_label'] }}</h2>
                            <div class="help-text">Ringkasan uang masuk dan keluar.</div>
                        </div>
                        <span class="summary-icon">{!! $iconSvg('wallet') !!}</span>
                    </div>

                    <div class="finance-row">
                        <span class="help-text">Member</span>
                        <strong>Rp{{ number_format($financialSummary['member_revenue'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="finance-row">
                        <span class="help-text">Daily pass</span>
                        <strong>Rp{{ number_format($financialSummary['daily_pass_revenue'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="finance-row">
                        <span class="help-text">Transaksi lain</span>
                        <strong>Rp{{ number_format($financialSummary['other_revenue'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="finance-row">
                        <span class="help-text">Pengeluaran</span>
                        <strong>Rp{{ number_format($financialSummary['total_expense'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="finance-row">
                        <span>Sisa bersih</span>
                        <strong>Rp{{ number_format($financialSummary['net_revenue'], 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="expense-card p-4 mt-4">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-4 flex-wrap">
                <div>
                    <div class="section-label">Pengeluaran</div>
                    <h2 class="h5 fw-bold mt-2 mb-1">Tambah pengeluaran</h2>
                    <div class="help-text">Catat biaya operasional agar sisa bersih laporan ikut akurat.</div>
                </div>
                <span class="summary-icon">{!! $iconSvg('expense') !!}</span>
            </div>

            <form method="POST" action="{{ route('admin.reports.expenses.store') }}">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-4">
                        <label class="form-label fw-semibold small mb-2" for="expense_title">Nama pengeluaran</label>
                        <input id="expense_title" type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Bayar listrik" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label class="form-label fw-semibold small mb-2" for="expense_category">Kategori</label>
                        <select id="expense_category" name="category" class="form-select" required>
                            <option value="">Pilih</option>
                            <option value="operasional" @selected(old('category') === 'operasional')>Operasional</option>
                            <option value="stok_barang" @selected(old('category') === 'stok_barang')>Stok Barang</option>
                            <option value="maintenance" @selected(old('category') === 'maintenance')>Maintenance</option>
                            <option value="gaji" @selected(old('category') === 'gaji')>Gaji</option>
                            <option value="lainnya" @selected(old('category') === 'lainnya')>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label class="form-label fw-semibold small mb-2" for="expense_amount">Nominal</label>
                        <input id="expense_amount" type="number" min="1" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="150000" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label class="form-label fw-semibold small mb-2" for="expense_method">Bayar</label>
                        <select id="expense_method" name="payment_method" class="form-select">
                            <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Cash</option>
                            <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label class="form-label fw-semibold small mb-2" for="expense_date">Tanggal</label>
                        <input id="expense_date" type="date" name="expense_date" class="form-control" value="{{ old('expense_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-12 col-lg-9">
                        <label class="form-label fw-semibold small mb-2" for="expense_notes">Catatan</label>
                        <input id="expense_notes" type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Opsional">
                    </div>
                    <div class="col-12 col-lg-3">
                        <button type="submit" class="btn btn-dark rounded-pill w-100 fw-semibold">Simpan Pengeluaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
