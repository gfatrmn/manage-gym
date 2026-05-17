@extends('admin.layout')

@section('content')
    <style>
        .report-detail-page .table th,
        .report-detail-page .table td {
            vertical-align: middle;
            padding: .95rem 1rem;
            white-space: normal;
            word-break: break-word;
        }

        .report-detail-page .form-control,
        .report-detail-page .btn {
            min-height: 44px;
        }

        .report-detail-toolbar,
        .report-detail-table-card,
        .metric-card,
        .topbar-card,
        .report-stat-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            background: rgba(18, 22, 34, 0.95);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.14);
        }

        .report-detail-toolbar {
            padding: 1.5rem 1.5rem;
        }

        .topbar-card {
            padding: 2.5rem 2rem;
            border-radius: 1.75rem;
        }

        .metric-card {
            min-height: 0;
        }

        .report-detail-actions .btn {
            min-width: 142px;
            font-weight: 700;
        }

        .report-detail-pill {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-muted);
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .01em;
        }

        .report-detail-category {
            display: inline-flex;
            align-items: center;
            padding: .4rem .9rem;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.16);
            color: var(--accent, #0d6efd);
            font-size: .8rem;
            font-weight: 700;
        }

        .report-detail-table-card .table thead th {
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, 0.72);
            border-bottom-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.06);
            position: sticky;
            top: 0;
            z-index: 1;
            white-space: nowrap;
        }

        .report-detail-table-card .table tbody tr {
            transition: background .2s ease;
        }

        .report-detail-table-card .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        .report-detail-table-card .table tbody tr + tr td {
            border-top-color: rgba(255, 255, 255, 0.08);
        }

        .report-detail-table-card .table-responsive {
            max-height: 64vh;
        }

        .report-month-form .form-label,
        .section-label {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255, 255, 255, 0.65);
        }

        .report-stat-card .table-responsive {
            max-height: 24rem;
        }

        .report-detail-page .muted-copy,
        .report-detail-page .small {
            color: rgba(255, 255, 255, 0.68);
        }

        .report-member-toggle {
            min-width: 120px;
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }

        .report-member-toggle.active,
        .report-member-toggle:hover,
        .report-section-toggle.active,
        .report-section-toggle:hover {
            color: #fff;
            background: rgba(13, 110, 253, 0.95);
            border-color: rgba(13, 110, 253, 0.95);
        }

        .report-section-toggle,
        .payment-filter {
            min-width: 112px;
            font-weight: 700;
        }

        .member-group-table,
        .report-section-table {
            transition: opacity .2s ease;
        }

        .report-detail-page h1,
        .report-detail-page h2,
        .report-detail-page h3 {
            color: #fff;
        }

        @media (max-width: 575.98px) {
            .report-detail-page .topbar-card {
                padding: 1rem !important;
            }

            .report-detail-actions .btn,
            .report-section-toggle,
            .payment-filter {
                width: 100%;
            }
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
                        <input type="hidden" name="detail_section" id="detail_section_input" value="{{ request()->query('detail_section', '') }}">
                        <label class="form-label fw-semibold small mb-2" for="detail_report_month">Filter per bulan</label>
                        <div class="input-group mb-3">
                            <input id="detail_report_month" type="month" name="detail_month" class="form-control" value="{{ $detailFilterMonth }}">
                            <button type="submit" class="btn btn-dark fw-semibold">Terapkan</button>
                        </div>
                        @if ($selectedReport['slug'] === 'laporan-member')
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small mb-2" for="detail_join_month">Bulan Tanggal Daftar</label>
                                    <input id="detail_join_month" type="month" name="detail_join_month" class="form-control" value="{{ $detailJoinMonth ?? '' }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold small mb-2" for="detail_expired_month">Bulan Tanggal Berakhir</label>
                                    <input id="detail_expired_month" type="month" name="detail_expired_month" class="form-control" value="{{ $detailExpiredMonth ?? '' }}">
                                </div>
                            </div>
                            <div class="small muted-copy mt-2">Gunakan filter bulan untuk membatasi daftar member berdasarkan tanggal daftar atau tanggal expired.</div>
                        @elseif ($selectedReport['slug'] === 'laporan-keuangan')
                            <div class="small muted-copy mt-2">Gunakan filter bulan untuk memilih periode laporan keuangan yang ingin ditampilkan.</div>
                        @else
                            <div class="small muted-copy mt-2">Gunakan filter bulan untuk memilih periode laporan yang ingin ditampilkan.</div>
                        @endif
                    </form>
                    <div class="d-grid gap-2 d-sm-flex justify-content-xl-end report-detail-actions">
                        <a href="{{ route('admin.reports', ['detail_filter' => 'month', 'detail_month' => $detailFilterMonth, 'detail_join_month' => $detailJoinMonth, 'detail_expired_month' => $detailExpiredMonth]) }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
                        <a href="{{ route('admin.reports.show', ['reportSlug' => $selectedReport['slug'], 'detail_filter' => 'month', 'detail_month' => $detailFilterMonth, 'detail_join_month' => $detailJoinMonth, 'detail_expired_month' => $detailExpiredMonth, 'export' => 1]) }}" class="btn btn-dark rounded-pill px-4">Unduh Excel</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="card report-detail-table-card p-0 overflow-hidden">
            <div class="p-4 border-bottom" style="background: rgba(255,255,255,.03);">
                <div class="section-label">Detail tabel</div>
                <h2 class="h5 fw-bold mt-2 mb-0">{{ $selectedReport['detail_title'] ?? ($selectedReport['slug'] === 'laporan-keuangan' ? 'Laporan keuangan' : 'Daftar member') }}</h2>
            </div>
            <div class="p-4">
                @if ($selectedReport['slug'] === 'laporan-member' && ! empty($selectedReport['member_table_groups']))
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($selectedReport['member_table_groups'] as $groupLabel => $groupMembers)
                            <button type="button" class="btn btn-outline-secondary rounded-pill report-section-toggle {{ $loop->first ? 'active' : '' }}" data-group="{{ $groupLabel }}">
                                {{ $groupLabel }} ({{ $groupMembers->count() }})
                            </button>
                        @endforeach
                    </div>

                    @foreach ($selectedReport['member_table_groups'] as $groupLabel => $groupMembers)
                        <div class="report-section-table {{ $loop->first ? '' : 'd-none' }}" data-group="{{ $groupLabel }}">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            @foreach ($selectedReport['member_table_columns'] as $column)
                                                <th>{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($groupMembers as $member)
                                            <tr>
                                                <td>{{ $member->full_name }}</td>
                                                <td>{{ $member->payment_method ? ucfirst($member->payment_method) : 'Reguler' }}</td>
                                                <td>{{ $member->joined_at?->format('d M Y') ?? '-' }}</td>
                                                <td>{{ $member->expires_at?->format('d M Y') ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($selectedReport['member_table_columns']) }}" class="text-center py-4 text-secondary">Tidak ada data untuk kategori ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @elseif (! empty($selectedReport['reportSections']))
                    @php $activeSection = request()->query('detail_section', ''); @endphp
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($selectedReport['reportSections'] as $sectionLabel => $sectionData)
                            <button type="button" class="btn btn-outline-secondary rounded-pill report-section-toggle {{ ($activeSection === $sectionLabel || (!$activeSection && $loop->first)) ? 'active' : '' }}" data-group="{{ $sectionLabel }}">
                                {{ $sectionLabel }}
                            </button>
                        @endforeach
                    </div>

                    @foreach ($selectedReport['reportSections'] as $sectionLabel => $sectionData)
                        <div class="report-section-table {{ ($activeSection === $sectionLabel || (!$activeSection && $loop->first)) ? '' : 'd-none' }}" data-group="{{ $sectionLabel }}">
                            @if ($sectionLabel === 'Pembayaran')
                                <div class="btn-group mb-3">
                                    <button type="button" class="btn btn-outline-secondary payment-filter active" data-filter="all">Semua</button>
                                    <button type="button" class="btn btn-outline-secondary payment-filter" data-filter="Member">Member</button>
                                    <button type="button" class="btn btn-outline-secondary payment-filter" data-filter="Non-member">Non-member</button>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            @foreach ($sectionData['columns'] as $column)
                                                <th>{{ $column }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sectionData['rows'] as $row)
                                            <tr @if($sectionLabel === 'Pembayaran') data-payment-type="{{ $row[2] ?? '' }}" @endif>
                                                @foreach ($row as $cell)
                                                    <td>{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($sectionData['columns']) }}" class="text-center py-4 text-secondary">Tidak ada data untuk bagian ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    @foreach ($selectedReport['columns'] ?? [] as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($selectedReport['rows'] ?? [] as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($selectedReport['columns'] ?? []) ?: 1 }}" class="text-center py-5 text-secondary">Belum ada data pada laporan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.report-section-toggle, .report-member-toggle');
            const tables = document.querySelectorAll('.report-section-table, .member-group-table');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const group = this.dataset.group;
                    buttons.forEach(btn => btn.classList.toggle('active', btn === this));
                    tables.forEach(table => table.classList.toggle('d-none', table.dataset.group !== group));
                });
            });

            const paymentFilters = document.querySelectorAll('.payment-filter');
            const paymentSection = document.querySelector('.report-section-table[data-group="Pembayaran"]');
            const paymentRows = paymentSection ? paymentSection.querySelectorAll('tbody tr') : [];
            const sectionInput = document.querySelector('#detail_section_input');

            const getPaymentType = row => row.dataset.paymentType || row.children[2]?.textContent.trim() || '';
            const applyPaymentFilter = filter => {
                paymentRows.forEach(function (row) {
                    const rowType = getPaymentType(row);
                    const hide = filter !== 'all' && rowType !== filter;
                    row.style.display = hide ? 'none' : '';
                });
            };

            paymentFilters.forEach(function (filterButton) {
                filterButton.addEventListener('click', function () {
                    const filter = this.dataset.filter;
                    paymentFilters.forEach(btn => btn.classList.toggle('active', btn === this));
                    applyPaymentFilter(filter);
                });
            });

            const sectionButtons = document.querySelectorAll('.report-section-toggle');
            sectionButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    if (sectionInput) {
                        sectionInput.value = this.dataset.group;
                    }
                });
            });

            if (paymentFilters.length > 0) {
                applyPaymentFilter('all');
            }
        });
    </script>
</div>
@endsection
