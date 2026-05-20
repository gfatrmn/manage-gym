@extends('admin.layout')

@section('content')
    <style>
        /* ... (Style tetap sama seperti sebelumnya) ... */
        .ds {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0.5rem 1.5rem 1rem;
            color: #fff;
        }

        .ds-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .ds-section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: .3rem;
        }

        .dashboard-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
        }

        .section-divider {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 1.5rem 0 1rem;
        }

        .section-divider span {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgb(255, 255, 255);
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 1.2rem;
            transition: 0.3s;
        }

        .stat-card:hover {
            border-color: rgba(255, 49, 49, 0.3);
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            color: rgb(255, 255, 255);
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-note {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.631);
            margin-top: 6px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            align-items: start;
        }

        .box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            overflow: hidden;
        }

        .box-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .box-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl th {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.3);
            padding: 1.2rem 1.5rem 0.8rem;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: 1px;
        }

        .tbl td {
            font-size: 13px;
            padding: 1.2rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.9);
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .col-nama { width: 25%; }
        .col-telp { width: 25%; }
        .col-tgl  { width: 30%; }
        .col-aktif { width: 25%; }

        .expires-val {
            font-weight: 700;
            color: #fff;
        }

        .type-pill {
            font-size: 9px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .type-member {
            background: rgba(0, 232, 122, 0.1);
            color: #00e87a;
        }

        .type-guest {
            background: rgba(59, 158, 255, 0.1);
            color: #3b9eff;
        }

        .date-text {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            font-weight: 600;
        }

        .phone-text {
            color: rgba(255, 255, 255, 0.7);
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        /* =============================================
           QUICK ACTION SECTION
           ============================================= */
        .quick-action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 0;
        }

        .quick-action-card {
            border-radius: 20px;
            padding: 1.4rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            border: 1px solid transparent;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            opacity: 0;
            transition: opacity 0.25s;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
        }

        .quick-action-card:hover::before {
            opacity: 1;
        }

        /* Check-in Card */
        .qac-checkin {
            background: rgba(25, 135, 84, 0.08);
            border-color: rgba(25, 135, 84, 0.2);
        }

        .qac-checkin:hover {
            border-color: rgba(25, 135, 84, 0.45);
            box-shadow: 0 8px 28px rgba(25, 135, 84, 0.15);
        }

        .qac-checkin::before {
            background: rgba(25, 135, 84, 0.05);
        }

        /* Add Member Card */
        .qac-addmember {
            background: rgba(220, 53, 69, 0.08);
            border-color: rgba(220, 53, 69, 0.2);
        }

        .qac-addmember:hover {
            border-color: rgba(220, 53, 69, 0.45);
            box-shadow: 0 8px 28px rgba(220, 53, 69, 0.15);
        }

        .qac-addmember::before {
            background: rgba(220, 53, 69, 0.05);
        }

        .qac-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .qac-icon-green  { background: rgba(25, 135, 84, 0.18); color: #34d399; }
        .qac-icon-red    { background: rgba(220, 53, 69, 0.18); color: #f87171; }

        .qac-text-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.615);
            margin-bottom: 3px;
            margin-right: 40px;
        }

        .qac-text-title {
            font-size: 1.2rem;
            font-weight: 700;
            /* margin-left: px; */
            color: #fff;
        }

        .qac-arrow {
            margin-left: auto;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.2);
            transition: color 0.2s, transform 0.2s;
        }

        .quick-action-card:hover .qac-arrow {
            color: rgba(255, 255, 255, 0.6);
            transform: translateX(3px);
        }

        /* =============================================
           MEMBER SEARCH INPUT (reused from checkin view)
           ============================================= */
        .member-search-wrap {
            position: relative;
            flex: 0 0 auto;
        }

        .member-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.35);
            font-size: 13px;
            pointer-events: none;
        }

        /* =============================================
           RESPONSIVE — Tablet (max-width: 1024px)
           ============================================= */
        @media (max-width: 1024px) {
            .ds { padding: 0.5rem 1.2rem 1rem; }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .dashboard-title { font-size: 1.8rem; }

            .quick-action-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }

        /* =============================================
           RESPONSIVE — Mobile (max-width: 640px)
           ============================================= */
        @media (max-width: 640px) {
            .ds { padding: 0.5rem 0.85rem 1.5rem; }

            .ds-header {
                margin-bottom: 1.25rem;
                padding-bottom: 1rem;
            }

            .dashboard-title { font-size: 1.5rem; }
            .ds-section-label { font-size: 10px; }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .stat-card {
                border-radius: 14px;
                padding: 0.9rem 1rem;
            }

            .stat-label { font-size: 10px; }
            .stat-value { font-size: 1.3rem; }
            .stat-note  { font-size: 10px; }

            .section-divider {
                margin: 1.2rem 0 0.75rem;
            }

            .section-divider span {
                font-size: 12px;
                letter-spacing: 1.5px;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }

            .box { border-radius: 14px; }

            .box-head { padding: 1rem 1.1rem; }

            .box-title {
                font-size: 11px;
                letter-spacing: 1px;
            }

            .tbl th {
                font-size: 9px;
                padding: 0.9rem 1.1rem 0.6rem;
            }

            .tbl td {
                font-size: 12px;
                padding: 0.85rem 1.1rem;
            }

            .type-pill {
                font-size: 8px;
                padding: 3px 8px;
            }

            .quick-action-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .quick-action-card { padding: 1.1rem 1.2rem; }

            .qac-icon {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
                border-radius: 12px;
            }

            .qac-text-title { font-size: 0.9rem; }
        }

        /* =============================================
           RESPONSIVE — Very small (max-width: 380px)
           ============================================= */
        @media (max-width: 380px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .stat-card { padding: 0.75rem 0.8rem; }
            .stat-value { font-size: 1.15rem; }

            .tbl th,
            .tbl td {
                padding-left: 0.85rem;
                padding-right: 0.85rem;
            }
        }
    </style>

    @php
        $paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];
    @endphp

    <div class="ds">
        <div class="ds-header">
            <div>
                <div class="ds-section-label">Arena Gym · Executive Dashboard</div>
                <h1 class="dashboard-title">Arena Dashboard</h1>
            </div>
        </div>

        <div class="section-divider">
            <span>Ringkasan Statistik</span>
        </div>

        <div class="stats-grid">
            {{-- Card 1: Total Member --}}
            <div class="stat-card">
                <div class="stat-label">{{ $stats[0]['label'] }}</div>
                <div class="stat-value">{{ $stats[0]['value'] }}</div>
                <div class="stat-note">{{ $stats[0]['note'] }}</div>
            </div>

            {{-- Card 2: Pemasukan --}}
            <div class="stat-card">
                <div class="stat-label">{{ $stats[1]['label'] }}</div>
                <div class="stat-value" style="color: #00e87a !important;">{{ $stats[1]['value'] }}</div>
                <div class="stat-note">{{ $stats[1]['note'] }}</div>
            </div>

            {{-- Card 3: Check-in Hari Ini --}}
            <div class="stat-card">
                <div class="stat-label">{{ $heroSummary[0]['label'] }}</div>
                <div class="stat-value">{{ $heroSummary[0]['value'] }}</div>
                <div class="stat-note">{{ $heroSummary[0]['note'] }}</div>
            </div>

            {{-- Card 4: Membership Alert --}}
            <div class="stat-card">
                <div class="stat-label">{{ $heroSummary[1]['label'] }}</div>
                <div class="stat-value text-warning">{{ $heroSummary[1]['value'] }}</div>
                <div class="stat-note">{{ $heroSummary[1]['note'] }}</div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SECTION: AKSI CEPAT --}}
        {{-- ============================================= --}}
        <div class="section-divider">
            <span>Aksi Cepat</span>
        </div>

        <div class="quick-action-grid">
            {{-- Quick Action: Check-in --}}
            <button class="quick-action-card qac-checkin" data-bs-toggle="modal" data-bs-target="#quickCheckinModal"
                style="border: 1px solid rgba(25,135,84,0.2);">
                <div class="qac-icon qac-icon-green">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <div class="qac-text-label">Proses Sekarang</div>
                    <div class="qac-text-title">Check-in Member</div>
                </div>
                <i class="fas fa-chevron-right qac-arrow"></i>
            </button>

            {{-- Quick Action: Tambah Member --}}
            <button class="quick-action-card qac-addmember" data-bs-toggle="modal" data-bs-target="#quickAddMemberModal"
                style="border: 1px solid rgba(220,53,69,0.2);">
                <div class="qac-icon qac-icon-red">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <div class="qac-text-label">Daftarkan Baru</div>
                    <div class="qac-text-title">Tambah Member</div>
                </div>
                <i class="fas fa-chevron-right qac-arrow"></i>
            </button>
        </div>

        <div class="section-divider">
            <span>Aktivitas Terbaru</span>
        </div>

        <div class="content-grid">
            {{-- Tabel Member Baru --}}
            <div class="box">
                <div class="box-head">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-plus text-danger"></i>
                        <span class="box-title">Registrasi Member Terbaru</span>
                    </div>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th class="col-nama">Nama Member</th>
                            <th class="col-telp">Telepon</th>
                            <th class="col-tgl">Tanggal Daftar</th>
                            <th class="col-aktif">Masa Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMembers as $m)
                            <tr>
                                <td class="fw-bold">{{ $m->full_name }}</td>
                                <td style="color: rgba(255,255,255,0.7)">{{ $m->phone }}</td>
                                <td class="expires-val">{{ $m->created_at }}</td>
                                <td class="expires-val">{{ $m->expires_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tabel Check-in Terbaru (Gabungan Member & Guest) --}}
            <div class="box">
                <div class="box-head">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-list-ul text-warning"></i>
                        <span class="box-title">Log Aktivitas Terkini</span>
                    </div>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentCheckins as $c)
                            <tr>
                                <td class="fw-bold">{{ $c['nama'] }}</td>
                                <td><span
                                        style="font-size: 10px; padding: 3px 8px; border-radius: 4px; background: rgba(255,255,255,0.1);">{{ $c['tipe'] }}</span>
                                </td>
                                <td class="date-text">{{ $c['waktu'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL: QUICK CHECK-IN (Desain sama dengan view checkins)       --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="quickCheckinModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-qrcode me-2 text-danger"></i>Member Check-in
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.checkins.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="small text-white-50 mb-3">Ketik nama atau kode member untuk mencari.</p>

                        {{-- Hidden select untuk menyimpan ID yang dipilih --}}
                        <select name="gym_member_id" id="quickMemberSelectHidden" style="display:none;" required>
                            <option value="">-- Pilih Member --</option>
                            @foreach ($memberOptions as $member)
                                <option value="{{ $member->id }}"
                                    data-name="{{ $member->full_name }}"
                                    data-code="{{ $member->checkin_code }}">
                                    {{ $member->full_name }} ({{ $member->checkin_code }})
                                </option>
                            @endforeach
                        </select>

                        {{-- Search input + dropdown --}}
                        <div style="position: relative;">
                            <div class="input-group">
                                <input type="text" id="quickMemberSearchInput"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 12px 0 0 12px;"
                                    placeholder="Cari nama atau kode member..."
                                    autocomplete="off">
                                <button type="submit" class="btn btn-danger px-4"
                                    style="border-radius: 0 12px 12px 0;">
                                    <i class="fas fa-check me-1"></i> Check In
                                </button>
                            </div>
                            <div id="quickMemberDropdown"
                                style="display:none; position:fixed; z-index:999999;
                                background:#1e1e2e; border:1px solid rgba(255,255,255,0.12);
                                border-radius:10px; max-height:220px; overflow-y:auto;">
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="d-flex align-items-center gap-3 my-4">
                            <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.08); margin: 0;">
                            <span class="small text-white-50">atau</span>
                            <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.08); margin: 0;">
                        </div>

                        {{-- Daily Guest shortcut --}}
                        <button type="button" class="btn w-100 fw-bold py-3"
                            style="border-radius: 12px; background: rgba(13,110,253,0.1); border: 1px solid rgba(13,110,253,0.25); color: #60a5fa;"
                            data-bs-dismiss="modal"
                            data-bs-toggle="modal"
                            data-bs-target="#quickAddGuestModal">
                            <i class="fas fa-user-plus me-2"></i> Input Tamu Harian (Guest)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL: QUICK ADD GUEST (dari halaman checkins)                 --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="quickAddGuestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">Input Tamu Harian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.checkins.guest.store') }}" method="POST">
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
                        <button type="submit"
                            class="btn btn-info w-100 rounded-pill fw-bold py-3 text-white">
                            Konfirmasi &amp; Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL: QUICK TAMBAH MEMBER (Desain sama dengan view members)   --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="quickAddMemberModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg" style="border-radius: 1.5rem;">
                <div class="modal-header border-bottom border-white border-opacity-10 p-4">
                    <h5 class="modal-title fw-bold">Tambah Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Nama Lengkap</label>
                            <input type="text" name="full_name"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Email</label>
                            <input type="email" name="email"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small text-uppercase fw-bold opacity-50">No. Telepon</label>
                                <input type="text" name="phone"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small text-uppercase fw-bold opacity-50">Metode Bayar</label>
                                <select name="payment_method"
                                    class="form-select bg-white bg-opacity-10 border-0 text-white p-3"
                                    style="border-radius: 0.8rem;" required>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm }}" class="text-dark">{{ $pm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Tgl Daftar</label>
                            <input type="date" name="joined_at"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-3"
                                style="border-radius: 0.8rem;" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small text-uppercase fw-bold opacity-50">Foto Profil</label>
                            <input type="file" name="profile_photo"
                                class="form-control bg-white bg-opacity-10 border-0 text-white p-2"
                                style="border-radius: 0.8rem;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit"
                            class="btn btn-danger w-100 rounded-pill fw-bold py-3">
                            Simpan Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SCRIPT: Member Search Dropdown (untuk Quick Check-in Modal)    --}}
    {{-- ============================================================== --}}
    <script>
        (function () {
            const searchInput  = document.getElementById('quickMemberSearchInput');
            const selectHidden = document.getElementById('quickMemberSelectHidden');
            const dropdown     = document.getElementById('quickMemberDropdown');

            // Angkat ke body agar mengambang di atas segalanya
            document.body.appendChild(dropdown);

            const allMembers = Array.from(selectHidden.options)
                .filter(o => o.value)
                .map(o => ({
                    value: o.value,
                    name:  o.dataset.name,
                    code:  o.dataset.code
                }));

            function positionDropdown() {
                const rect      = searchInput.getBoundingClientRect();
                const dropH     = dropdown.offsetHeight;
                dropdown.style.top   = (rect.top - dropH - 4) + 'px';
                dropdown.style.left  = rect.left + 'px';
                dropdown.style.width = rect.width + 'px';
            }

            function renderDropdown(query) {
                const q = query.toLowerCase().trim();
                const filtered = q
                    ? allMembers.filter(m =>
                        m.name.toLowerCase().includes(q) ||
                        m.code.toLowerCase().includes(q))
                    : allMembers;

                dropdown.innerHTML = '';

                if (!filtered.length) {
                    dropdown.innerHTML =
                        '<div style="padding:12px 16px;color:rgba(255,255,255,.4);font-size:13px;">Tidak ada member ditemukan</div>';
                } else {
                    filtered.forEach(m => {
                        const div = document.createElement('div');
                        div.style.cssText =
                            'padding:10px 16px;color:#fff;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.05);font-size:14px;';
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
                positionDropdown();
            }

            searchInput.addEventListener('input',  () => { selectHidden.value = ''; renderDropdown(searchInput.value); });
            searchInput.addEventListener('focus',  () => renderDropdown(searchInput.value));
            searchInput.addEventListener('blur',   () => setTimeout(() => dropdown.style.display = 'none', 150));
            window.addEventListener('scroll', () => { if (dropdown.style.display !== 'none') positionDropdown(); }, true);
            window.addEventListener('resize', () => { if (dropdown.style.display !== 'none') positionDropdown(); });

            // Reset dropdown & input saat modal dibuka
            document.getElementById('quickCheckinModal').addEventListener('shown.bs.modal', function () {
                searchInput.value  = '';
                selectHidden.value = '';
                dropdown.style.display = 'none';
                searchInput.focus();
            });
        })();
    </script>
@endsection
