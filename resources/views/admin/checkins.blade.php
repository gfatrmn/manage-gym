@extends('admin.layout')

@section('content')
    @php
        $paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];
    @endphp

    <style>
        /* ── Root & Base ─────────────────────────────── */
        .ci-page {
            padding: 1rem 2rem 3rem;
            color: #fff;
        }

        /* ── Header ──────────────────────────────────── */
        .ci-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .ci-section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: rgba(255,255,255,0.4);
            margin-bottom: .3rem;
        }

        .ci-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .ci-clock-wrap .ci-date {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            text-align: right;
        }

        .ci-clock-wrap .ci-clock {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            text-align: right;
            letter-spacing: 2px;
        }

        /* ── Stats Cards ─────────────────────────────── */
        .ci-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .ci-stat {
            border-radius: 16px;
            padding: 1.1rem 1.3rem;
            border: 1px solid transparent;
            transition: transform 0.25s;
        }

        .ci-stat:hover { transform: translateY(-2px); }

        .ci-stat-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .ci-stat-val {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .ci-stat-note {
            font-size: 11px;
            margin-top: 4px;
            opacity: .55;
        }

        .ci-stat-green {
            background: rgba(25,135,84,0.08);
            border-color: rgba(25,135,84,0.2);
        }
        .ci-stat-green .ci-stat-label { color: #34d399; }
        .ci-stat-green .ci-stat-val   { color: #34d399; }

        .ci-stat-blue {
            background: rgba(59,130,246,0.08);
            border-color: rgba(59,130,246,0.2);
        }
        .ci-stat-blue .ci-stat-label { color: #60a5fa; }
        .ci-stat-blue .ci-stat-val   { color: #60a5fa; }

        .ci-stat-purple {
            background: rgba(168,85,247,0.08);
            border-color: rgba(168,85,247,0.2);
        }
        .ci-stat-purple .ci-stat-label { color: #c084fc; }
        .ci-stat-purple .ci-stat-val   { color: #c084fc; }

        .ci-stat-amber {
            background: rgba(245,158,11,0.08);
            border-color: rgba(245,158,11,0.2);
        }
        .ci-stat-amber .ci-stat-label { color: #fbbf24; }
        .ci-stat-amber .ci-stat-val   { color: #fbbf24; }

        /* ── Action Panel ────────────────────────────── */
        .ci-action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .ci-panel {
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 18px;
            padding: 1.4rem 1.5rem;
        }

        .ci-panel-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ci-panel-desc {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 1rem;
        }

        /* ── Search Input ────────────────────────────── */
        .ci-input-group {
            display: flex;
            gap: 0;
        }

        .ci-input {
            flex: 1;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #fff;
            font-size: 13px;
            padding: 0.6rem 1rem 0.6rem 1rem;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .ci-input::placeholder { color: rgba(255,255,255,0.3); }
        .ci-input:focus {
            border-color: rgba(220,53,69,0.5);
            background: rgba(255,255,255,0.1);
        }

        .ci-btn-submit {
            background: #dc3545;
            border: none;
            border-radius: 0 10px 10px 0;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 0 1.2rem;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .ci-btn-submit:hover { background: #b91c2c; }

        .ci-btn-guest {
            width: 100%;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.25);
            border-radius: 10px;
            color: #60a5fa;
            font-size: 13px;
            font-weight: 700;
            padding: 0.7rem 1rem;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            margin-top: auto;
        }

        .ci-btn-guest:hover {
            background: rgba(59,130,246,0.2);
            border-color: rgba(59,130,246,0.45);
        }

        /* ── Log Panel ───────────────────────────────── */
        .ci-log-panel {
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 18px;
            overflow: hidden;
        }

        .ci-log-head {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .ci-log-head-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ci-log-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* ── Filter Bar ──────────────────────────────── */
        .ci-filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ci-filter-input {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            padding: 0.38rem 0.75rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .ci-filter-input:focus { border-color: rgba(220,53,69,0.5); }
        .ci-filter-input::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.5; }

        .ci-filter-select {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            padding: 0.38rem 0.75rem;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .ci-filter-select:focus { border-color: rgba(220,53,69,0.5); }
        .ci-filter-select option { background: #1e1e2e; color: #fff; }

        .ci-filter-btn {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 0.38rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .ci-filter-btn:hover { background: #b91c2c; }

        .ci-filter-reset {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            font-weight: 600;
            padding: 0.38rem 0.8rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .ci-filter-reset:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        /* ── Tab Pills ───────────────────────────────── */
        .ci-tabs {
            display: flex;
            gap: 4px;
            background: rgba(255,255,255,0.04);
            padding: 4px;
            border-radius: 10px;
        }

        .ci-tab-btn {
            background: none;
            border: none;
            border-radius: 7px;
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            font-weight: 700;
            padding: 0.4rem 1rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .ci-tab-btn.active {
            background: #dc3545;
            color: #fff;
        }

        /* ── Table ───────────────────────────────────── */
        .ci-tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .ci-tbl thead th {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-weight: 600;
            white-space: nowrap;
        }

        .ci-tbl tbody td {
            font-size: 13px;
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            color: rgba(255,255,255,0.85);
            vertical-align: middle;
        }

        .ci-tbl tbody tr:last-child td { border-bottom: none; }

        .ci-tbl tbody tr {
            transition: background 0.15s;
        }

        .ci-tbl tbody tr:hover {
            background: rgba(255,255,255,0.025);
        }

        /* Badges */
        .badge-member {
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            background: rgba(52,211,153,0.12);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.2);
        }

        .badge-guest {
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
            background: rgba(96,165,250,0.12);
            color: #60a5fa;
            border: 1px solid rgba(96,165,250,0.2);
        }

        .badge-verified {
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
            background: rgba(52,211,153,0.08);
            color: #34d399;
            border: 1px solid rgba(52,211,153,0.15);
        }

        .badge-pay {
            font-size: 10px;
            padding: 3px 9px;
            border-radius: 6px;
            background: rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.7);
        }

        /* Pagination */
        .ci-pagination {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .ci-pagination .pagination {
            margin: 0;
            gap: 4px;
        }

        .ci-pagination .page-link {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.55);
            border-radius: 7px !important;
            font-size: 12px;
            padding: 0.4rem 0.75rem;
            transition: all 0.2s;
        }

        .ci-pagination .page-item.active .page-link {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff;
            font-weight: 700;
        }

        .ci-pagination .page-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .ci-pagination .page-item.disabled .page-link {
            background: rgba(255,255,255,0.02);
            color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.04);
        }

        .ci-pagination nav p { display: none !important; }

        .ci-pag-info {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            white-space: nowrap;
        }

        /* Empty state */
        .ci-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: rgba(255,255,255,0.25);
            font-size: 13px;
        }

        .ci-empty i {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            display: block;
            opacity: 0.3;
        }

        /* ── Alert ───────────────────────────────────── */
        .ci-alert {
            background: rgba(25,135,84,0.12);
            border: 1px solid rgba(25,135,84,0.25);
            border-radius: 12px;
            padding: 0.65rem 1rem;
            font-size: 13px;
            color: #34d399;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Responsive ──────────────────────────────── */
        @media (max-width: 1024px) {
            .ci-page { padding: 1rem 1.2rem 2rem; }
            .ci-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .ci-action-grid { grid-template-columns: 1fr; }
            .ci-log-head { flex-direction: column; align-items: flex-start; }
            .ci-filter-bar { width: 100%; }
        }

        @media (max-width: 640px) {
            .ci-page { padding: 0.75rem 0.85rem 2rem; }
            .ci-title { font-size: 1.5rem; }
            .ci-stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .ci-stat-val { font-size: 1.4rem; }
            .ci-header { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .ci-clock-wrap .ci-clock { text-align: left; font-size: 1.2rem; }
            .ci-clock-wrap .ci-date { text-align: left; }

            /* Hide less important columns on mobile */
            .hide-mobile { display: none !important; }
        }
    </style>

    <div class="ci-page">

        {{-- Header --}}
        <div class="ci-header">
            <div>
                <div class="ci-section-label">Arena Gym · Management Check-in</div>
                <h1 class="ci-title">Check-in Member &amp; Guest</h1>
            </div>
            <div class="ci-clock-wrap">
                <div class="ci-date">{{ now()->translatedFormat('l, d F Y') }}</div>
                <div class="ci-clock" id="liveClock">00:00:00</div>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('status'))
            <div class="ci-alert">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="ci-stats-grid">
            <div class="ci-stat ci-stat-green">
                <div class="ci-stat-label">Member Check-in Hari Ini</div>
                <div class="ci-stat-val">{{ $todayCheckinsCount }}</div>
                <div class="ci-stat-note">Terverifikasi</div>
            </div>
            <div class="ci-stat ci-stat-blue">
                <div class="ci-stat-label">Tamu Harian Hari Ini</div>
                <div class="ci-stat-val">{{ $todayGuestsCount }}</div>
                <div class="ci-stat-note">Daily Guest</div>
            </div>
            <div class="ci-stat ci-stat-purple">
                <div class="ci-stat-label">Total Kunjungan Hari Ini</div>
                <div class="ci-stat-val">{{ $todayCheckinsCount + $todayGuestsCount }}</div>
                <div class="ci-stat-note">Member + Guest</div>
            </div>
            <div class="ci-stat ci-stat-amber">
                <div class="ci-stat-label">Pemasukan Guest Hari Ini</div>
                <div class="ci-stat-val">
                    Rp {{ number_format($todayGuestRevenue ?? 0, 0, ',', '.') }}
                </div>
                <div class="ci-stat-note">Dari tamu harian</div>
            </div>
        </div>

        {{-- Action Panels --}}
        <div class="ci-action-grid">
            {{-- Member Check-in --}}
            <div class="ci-panel">
                <div class="ci-panel-title">
                    <i class="fas fa-qrcode text-danger"></i> Member Check-in
                </div>
                <div class="ci-panel-desc">Ketik nama atau kode member untuk mencari &amp; check-in.</div>

                <form action="{{ url('/admin/checkins') }}" method="POST">
                    @csrf
                    <select name="gym_member_id" id="memberSelectHidden" style="display:none;" required>
                        <option value="">-- Pilih Member --</option>
                        @foreach ($memberOptions as $member)
                            <option value="{{ $member->id }}"
                                data-name="{{ $member->full_name }}"
                                data-code="{{ $member->checkin_code }}">
                                {{ $member->full_name }} ({{ $member->checkin_code }})
                            </option>
                        @endforeach
                    </select>

                    <div style="position: relative;">
                        <div class="ci-input-group">
                            <input type="text" id="memberSearchInput" class="ci-input"
                                placeholder="Cari nama atau kode member..." autocomplete="off">
                            <button type="submit" class="ci-btn-submit">
                                <i class="fas fa-check me-1"></i> Check In
                            </button>
                        </div>
                        <div id="memberDropdown"
                            style="display:none; position:fixed; z-index:999999;
                                   background:#1e1e2e; border:1px solid rgba(255,255,255,0.12);
                                   border-radius:10px; max-height:220px; overflow-y:auto;">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Daily Guest --}}
            <div class="ci-panel d-flex flex-column">
                <div class="ci-panel-title">
                    <i class="fas fa-user-plus text-info"></i> Daily Guest (Tamu Harian)
                </div>
                <div class="ci-panel-desc">Input tamu yang membayar kunjungan harian.</div>
                <button class="ci-btn-guest mt-auto" data-bs-toggle="modal" data-bs-target="#addGuestModal">
                    <i class="fas fa-ticket-alt me-2"></i> Input Tamu Harian
                </button>
            </div>
        </div>

        {{-- Log Panel --}}
        <div class="ci-log-panel">
            <div class="ci-log-head">
                <div class="ci-log-head-left">
                    <i class="fas fa-list-ul text-warning"></i>
                    <span class="ci-log-title">Riwayat Check-in &amp; Guest</span>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ url()->current() }}" class="ci-filter-bar">
                    {{-- Date From --}}
                    <input type="date" name="date_from" class="ci-filter-input"
                        value="{{ request('date_from') }}"
                        placeholder="Dari tanggal">

                    {{-- Date To --}}
                    <input type="date" name="date_to" class="ci-filter-input"
                        value="{{ request('date_to') }}"
                        placeholder="Sampai tanggal">

                    {{-- Type Filter --}}
                    <select name="type" class="ci-filter-select">
                        <option value="">Semua Tipe</option>
                        <option value="member" {{ request('type') === 'member' ? 'selected' : '' }}>Member</option>
                        <option value="guest"  {{ request('type') === 'guest'  ? 'selected' : '' }}>Guest</option>
                    </select>

                    <button type="submit" class="ci-filter-btn">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>

                    @if (request()->hasAny(['date_from','date_to','type']))
                        <a href="{{ url()->current() }}" class="ci-filter-reset">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- Tab --}}
                <div class="ci-tabs" id="ciTabGroup">
                    <button class="ci-tab-btn active" data-tab="all">Semua</button>
                    <button class="ci-tab-btn" data-tab="member">Member</button>
                    <button class="ci-tab-btn" data-tab="guest">Guest</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="ci-tbl">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th class="hide-mobile">Info Tambahan</th>
                            <th class="hide-mobile">Metode / Status</th>
                            <th class="hide-mobile">Nominal</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="ciTableBody">
                        @php $no = ($allLogs->currentPage() - 1) * $allLogs->perPage() + 1; @endphp

                        @forelse ($allLogs as $log)
                            <tr class="ci-row" data-type="{{ $log['type'] }}">
                                <td class="text-white-50" style="font-size: 11px;">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-bold" style="color: #fff;">{{ $log['nama'] }}</div>
                                    @if (!empty($log['sub']))
                                        <div class="small" style="color: rgba(255,255,255,0.35); font-size: 11px;">{{ $log['sub'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($log['type'] === 'member')
                                        <span class="badge-member">Member</span>
                                    @else
                                        <span class="badge-guest">Guest</span>
                                    @endif
                                </td>
                                <td class="hide-mobile" style="color: rgba(255,255,255,0.5); font-size: 12px;">
                                    {{ $log['info'] ?? '-' }}
                                </td>
                                <td class="hide-mobile">
                                    @if ($log['type'] === 'member')
                                        <span class="badge-verified">Verified</span>
                                    @else
                                        <span class="badge-pay">{{ $log['payment_method'] ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="hide-mobile fw-bold" style="font-size: 13px; color: rgba(255,255,255,0.7);">
                                    @if ($log['type'] === 'guest' && isset($log['amount']))
                                        <span style="color: #60a5fa;">Rp {{ number_format($log['amount'], 0, ',', '.') }}</span>
                                    @else
                                        <span style="color: rgba(255,255,255,0.2);">—</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: rgba(255,255,255,0.5); font-weight: 600; white-space: nowrap;">
                                    {{ $log['waktu'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ci-empty">
                                        <i class="fas fa-inbox"></i>
                                        Tidak ada data check-in
                                        @if (request()->hasAny(['date_from','date_to','type']))
                                            untuk filter yang dipilih
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($allLogs->hasPages())
                <div class="ci-pagination">
                    <div class="ci-pag-info">
                        Menampilkan {{ $allLogs->firstItem() }}–{{ $allLogs->lastItem() }}
                        dari {{ $allLogs->total() }} entri
                    </div>
                    <div class="custom-pagination">
                        {{ $allLogs->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="ci-pagination">
                    <div class="ci-pag-info">Total {{ $allLogs->total() }} entri</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── MODAL: Add Guest ──────────────────────────── --}}
    <div class="modal fade" id="addGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-plus me-2 text-info"></i>Input Tamu Harian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ url('/admin/checkins/guest') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Nama Tamu</label>
                            <input type="text" name="name"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Harga (Rp)</label>
                            <input type="number" name="price"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" value="25000" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Metode Pembayaran</label>
                            <select name="payment_method"
                                class="form-select bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required>
                                @foreach ($paymentMethods as $pm)
                                    <option value="{{ $pm }}" class="text-dark">{{ $pm }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-info w-100 rounded-pill fw-bold py-3 text-white">
                            <i class="fas fa-check me-2"></i>Konfirmasi &amp; Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Scripts ───────────────────────────────────── --}}
    <script>
        // Live Clock
        (function () {
            function tick() {
                document.getElementById('liveClock').innerText =
                    new Date().toLocaleTimeString('id-ID', { hour12: false });
            }
            setInterval(tick, 1000);
            tick();
        })();
    </script>

    <script>
        // Member Search Dropdown
        (function () {
            const searchInput  = document.getElementById('memberSearchInput');
            const selectHidden = document.getElementById('memberSelectHidden');
            const dropdown     = document.getElementById('memberDropdown');

            document.body.appendChild(dropdown);

            const allMembers = Array.from(selectHidden.options)
                .filter(o => o.value)
                .map(o => ({ value: o.value, name: o.dataset.name, code: o.dataset.code }));

            function position() {
                const r = searchInput.getBoundingClientRect();
                const h = dropdown.offsetHeight;
                // Show above if not enough space below
                const spaceBelow = window.innerHeight - r.bottom;
                if (spaceBelow < h + 8 && r.top > h + 8) {
                    dropdown.style.top  = (r.top - h - 4) + 'px';
                } else {
                    dropdown.style.top  = (r.bottom + 4) + 'px';
                }
                dropdown.style.left  = r.left + 'px';
                dropdown.style.width = r.width + 'px';
            }

            function render(query) {
                const q = query.toLowerCase().trim();
                const list = q
                    ? allMembers.filter(m => m.name.toLowerCase().includes(q) || m.code.toLowerCase().includes(q))
                    : allMembers;

                dropdown.innerHTML = '';
                if (!list.length) {
                    dropdown.innerHTML =
                        '<div style="padding:12px 16px;color:rgba(255,255,255,.4);font-size:13px;">Tidak ada member ditemukan</div>';
                } else {
                    list.forEach(m => {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding:10px 16px;color:#fff;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.05);font-size:14px;';
                        div.innerHTML = `${m.name} <span style="font-size:11px;opacity:.5;">${m.code}</span>`;
                        div.addEventListener('mousedown', e => {
                            e.preventDefault();
                            searchInput.value  = `${m.name} (${m.code})`;
                            selectHidden.value = m.value;
                            dropdown.style.display = 'none';
                        });
                        div.addEventListener('mouseover', () => div.style.background = 'rgba(220,53,69,.15)');
                        div.addEventListener('mouseout',  () => div.style.background = '');
                        dropdown.appendChild(div);
                    });
                }
                dropdown.style.display = 'block';
                position();
            }

            searchInput.addEventListener('input',  () => { selectHidden.value = ''; render(searchInput.value); });
            searchInput.addEventListener('focus',  () => render(searchInput.value));
            searchInput.addEventListener('blur',   () => setTimeout(() => dropdown.style.display = 'none', 150));
            window.addEventListener('scroll', () => { if (dropdown.style.display !== 'none') position(); }, true);
            window.addEventListener('resize', () => { if (dropdown.style.display !== 'none') position(); });
        })();
    </script>

    <script>
        // Client-side Tab filter (Semua / Member / Guest)
        (function () {
            const btns = document.querySelectorAll('#ciTabGroup .ci-tab-btn');
            const rows = document.querySelectorAll('#ciTableBody .ci-row');

            btns.forEach(btn => {
                btn.addEventListener('click', function () {
                    btns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const tab = this.dataset.tab;
                    rows.forEach(row => {
                        row.style.display = (tab === 'all' || row.dataset.type === tab) ? '' : 'none';
                    });
                });
            });
        })();
    </script>
@endsection
