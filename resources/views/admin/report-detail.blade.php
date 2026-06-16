@extends('admin.layout')

@section('content')
    <style>
        .report-detail-page .detail-card,
        .report-detail-page .filter-card,
        .report-detail-page .table-card {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            background: var(--panel-bg);
            box-shadow: var(--shadow-soft);
        }

        .report-detail-page .help-text {
            color: var(--text-muted);
            font-size: .92rem;
            line-height: 1.55;
        }

        .report-detail-page .report-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .8rem;
            font-weight: 700;
        }

        .report-detail-page .section-button {
            min-height: 42px;
            border-radius: 999px;
            font-weight: 700;
        }

        .report-detail-page .section-button.active,
        .report-detail-page .section-button:hover {
            color: #fff;
            background: rgba(255, 59, 59, .92);
            border-color: rgba(255, 59, 59, .92);
        }

        .report-detail-page .table th {
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--text-muted);
            border-bottom-color: var(--border);
            background: rgba(255, 255, 255, .04);
            white-space: nowrap;
        }

        .report-detail-page .table td {
            vertical-align: middle;
            padding: .9rem 1rem;
            border-top-color: var(--border);
            white-space: normal;
            word-break: break-word;
        }

        .report-detail-page .table tbody tr:hover {
            background: rgba(255, 255, 255, .03);
        }

        .report-detail-page .table-responsive {
            max-height: 68vh;
        }

        .report-detail-page .btn,
        .report-detail-page .form-control {
            min-height: 44px;
        }

        @media (max-width: 575.98px) {
            .report-detail-page .detail-card,
            .report-detail-page .filter-card,
            .report-detail-page .table-card {
                padding: 1rem !important;
            }

            .report-detail-page .action-button {
                width: 100%;
            }
        }
    </style>

    @php
        $activeSection = request()->query('detail_section', '');
        $tableTitle = $selectedReport['detail_title'] ?? match ($selectedReport['slug']) {
            'laporan-member' => 'Daftar member',
            'laporan-keuangan' => 'Rincian keuangan',
            'laporan-kehadiran' => 'Daftar check-in',
            'laporan-stok-barang' => 'Daftar stok barang',
            default => 'Detail laporan',
        };
    @endphp

    <div class="report-detail-page">
        <div class="detail-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-xl-7">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="brand-logo">
                        <div>
                            <div class="section-label">Detail Laporan</div>
                            <h1 class="h2 fw-bold mt-2 mb-2">{{ $selectedReport['title'] }}</h1>
                            <p class="help-text mb-3">{{ $selectedReport['summary'] }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="report-pill">{{ $selectedReport['group'] }}</span>
                                <span class="report-pill">Periode: {{ $detailFilterLabel }}</span>
                                <span class="report-pill">{{ $selectedReport['count_label'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-5">
                    <div class="filter-card p-3">
                        <form method="GET" action="{{ route('admin.reports.show', ['reportSlug' => $selectedReport['slug']]) }}">
                            <input type="hidden" name="detail_filter" value="month">
                            <input type="hidden" name="detail_section" id="detail_section_input" value="{{ $activeSection }}">
                            <label class="form-label fw-semibold small mb-2" for="detail_report_month">Bulan laporan</label>
                            <div class="input-group mb-3">
                                <input id="detail_report_month" type="month" name="detail_month" class="form-control" value="{{ $detailFilterMonth }}">
                                <button type="submit" class="btn btn-dark fw-semibold px-4">Tampilkan</button>
                            </div>
                        </form>
                        <div class="d-grid d-sm-flex gap-2">
                            <a href="{{ route('admin.reports', ['detail_filter' => 'month', 'detail_month' => $detailFilterMonth]) }}" class="btn btn-outline-secondary rounded-pill px-4 action-button">Kembali</a>
                            <a href="{{ route('admin.reports.show', ['reportSlug' => $selectedReport['slug'], 'detail_filter' => 'month', 'detail_month' => $detailFilterMonth, 'export' => 1]) }}" class="btn btn-dark rounded-pill px-4 action-button">Unduh Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card overflow-hidden">
            <div class="p-4 border-bottom" style="border-color: var(--border) !important;">
                <div class="section-label">Tabel Data</div>
                <h2 class="h5 fw-bold mt-2 mb-0">{{ $tableTitle }}</h2>
            </div>

            <div class="p-4">
                @if ($selectedReport['slug'] === 'laporan-member' && ! empty($selectedReport['member_table_groups']))
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($selectedReport['member_table_groups'] as $groupLabel => $groupMembers)
                            <button type="button" class="btn btn-outline-secondary section-button {{ $loop->first ? 'active' : '' }}" data-group="{{ $groupLabel }}">
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
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ($selectedReport['reportSections'] as $sectionLabel => $sectionData)
                            <button type="button" class="btn btn-outline-secondary section-button {{ ($activeSection === $sectionLabel || (!$activeSection && $loop->first)) ? 'active' : '' }}" data-group="{{ $sectionLabel }}">
                                {{ $sectionLabel }}
                            </button>
                        @endforeach
                    </div>

                    @foreach ($selectedReport['reportSections'] as $sectionLabel => $sectionData)
                        <div class="report-section-table {{ ($activeSection === $sectionLabel || (!$activeSection && $loop->first)) ? '' : 'd-none' }}" data-group="{{ $sectionLabel }}">
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
                                            <tr>
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
            const buttons = document.querySelectorAll('.section-button');
            const tables = document.querySelectorAll('.report-section-table');
            const sectionInput = document.querySelector('#detail_section_input');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const group = this.dataset.group;
                    buttons.forEach(btn => btn.classList.toggle('active', btn === this));
                    tables.forEach(table => table.classList.toggle('d-none', table.dataset.group !== group));
                    if (sectionInput) sectionInput.value = group;
                });
            });
        });
    </script>
@endsection
