@extends('admin.layout')

@section('content')
    <style>
        .report-detail-page .table th,
        .report-detail-page .table td {
            vertical-align: middle;
        }

        .report-detail-toolbar,
        .report-detail-table-card {
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .report-detail-pill {
            display: inline-flex;
            align-items: center;
            padding: .45rem .8rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
            font-size: .85rem;
            font-weight: 600;
        }

        .report-detail-category {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.12);
            color: var(--accent, #0d6efd);
            font-size: .78rem;
            font-weight: 700;
        }

        .report-detail-table-card .table thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
            border-bottom-color: var(--border);
        }

        .report-detail-table-card .table tbody tr + tr td {
            border-top-color: var(--border);
        }

        .report-detail-table-card .table-responsive {
            max-height: 72vh;
        }

        .report-month-form .form-label {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .report-stat-card {
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .report-stat-card .table-responsive {
            max-height: 24rem;
        }
    </style>

    <div class="report-detail-page">
        <div class="topbar-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-start">
                <div class="col-12 col-xl-8">
                    <span class="report-detail-category">{{ $selectedReport['group'] }}</span>
                    <h1 class="display-6 fw-bold mt-3 mb-3">{{ $selectedReport['title'] }}</h1>
                    <p class="muted-copy mb-0">{{ $selectedReport['summary'] }}</p>
                </div>
                <div class="col-12 col-xl-4">
                    <form method="GET" action="{{ route('admin.reports.show', ['reportSlug' => $selectedReport['slug']]) }}" class="report-month-form mb-3">
                        <input type="hidden" name="detail_filter" value="month">
                        <label class="form-label fw-semibold small mb-2" for="detail_report_month">Bulan laporan</label>
                        <div class="input-group">
                            <input id="detail_report_month" type="month" name="detail_month" class="form-control" value="{{ $detailFilterMonth }}">
                            <button type="submit" class="btn btn-dark">Terapkan</button>
                        </div>
                    </form>
                    <div class="d-grid gap-2 d-sm-flex justify-content-xl-end">
                        <a href="{{ route('admin.reports', ['detail_filter' => 'month', 'detail_month' => $detailFilterMonth]) }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali ke daftar</a>
                        <a href="{{ route('admin.reports.show', ['reportSlug' => $selectedReport['slug'], 'detail_filter' => 'month', 'detail_month' => $detailFilterMonth, 'export' => 1]) }}" class="btn btn-dark rounded-pill px-4">Export Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Jumlah Data</div>
                    <div class="fs-4 fw-bold mt-2">{{ $selectedReport['count_label'] }}</div>
                    <div class="small muted-copy mt-2">Total data yang tampil untuk laporan ini.</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Tanggal Laporan</div>
                    <div class="fs-5 fw-bold mt-2">{{ $selectedReport['date_label'] }}</div>
                    <div class="small muted-copy mt-2">Tanggal atau periode utama yang melekat pada laporan ini.</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Highlight</div>
                    <div class="fs-4 fw-bold mt-2">{{ $selectedReport['highlight'] }}</div>
                    <div class="small muted-copy mt-2">Angka atau indikator utama yang perlu diperhatikan lebih dulu.</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="metric-card p-4 h-100">
                    <div class="section-label">Filter Aktif</div>
                    <div class="fs-4 fw-bold mt-2">{{ $detailFilterLabel }}</div>
                    <div class="small muted-copy mt-2">Periode data mengikuti filter yang diterapkan dari halaman laporan.</div>
                </div>
            </div>
        </div>

        <div class="card report-detail-toolbar p-4 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-8">
                    <div class="section-label">Isi laporan</div>
                    <h2 class="h4 fw-bold mt-2 mb-1">Tabel detail {{ strtolower($selectedReport['title']) }}</h2>
                    <div class="small muted-copy">Semua data ditampilkan dalam tabel Bootstrap agar mudah dibaca dan siap diexport ke Excel.</div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <span class="report-detail-pill">{{ $selectedReport['group'] }}</span>
                        <span class="report-detail-pill">{{ $selectedReport['date_label'] }}</span>
                        <span class="report-detail-pill">{{ $selectedReport['count_label'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card report-stat-card p-4 h-100">
                    <div class="section-label">Statistik Harian</div>
                    <h3 class="h5 fw-bold mt-2 mb-1">Per hari dalam {{ $detailFilterLabel }}</h3>
                    <div class="small muted-copy mb-3">Gunakan tabel ini untuk membaca pergerakan laporan harian selama bulan yang sedang dipilih.</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    @foreach ($selectedReport['daily_stat_columns'] as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedReport['daily_stat_rows'] as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card report-stat-card p-4 h-100">
                    <div class="section-label">Statistik Bulanan</div>
                    <h3 class="h5 fw-bold mt-2 mb-1">Setiap bulan dalam {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $detailFilterMonth)->format('Y') }}</h3>
                    <div class="small muted-copy mb-3">Ringkasan ini membantu membandingkan performa laporan antar bulan dalam satu tahun aktif.</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    @foreach ($selectedReport['monthly_stat_columns'] as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedReport['monthly_stat_rows'] as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card report-detail-table-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            @foreach ($selectedReport['columns'] as $column)
                                <th>{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($selectedReport['rows'] as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($selectedReport['columns']) }}" class="text-center py-5 text-secondary">Belum ada data pada laporan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
