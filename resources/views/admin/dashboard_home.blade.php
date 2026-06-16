@extends('admin.layout')

@section('content')
    <style>
        /* ... (Style tetap sama seperti sebelumnya) ... */
        body.page-dashboard .container-fluid {
            width: 100%;
            max-width: none;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        body.page-dashboard .row {
            --bs-gutter-x: 0;
        }

        body.page-dashboard main.col-12 {
            width: 100%;
            max-width: none;
            flex: 0 0 100%;
        }

        .ds {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0.25rem 0 1rem;
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
            gap: 16px;
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
            font-size: 12px;
            text-transform: uppercase;
            color: rgb(255, 255, 255);
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-note {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.558);
            margin-top: 6px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
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

        .type-daily-pass {
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
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
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

        /* Daily Pass Card */
        .qac-daily-pass {
            background: rgba(13, 110, 253, 0.08);
            border-color: rgba(13, 110, 253, 0.2);
        }

        .qac-daily-pass:hover {
            border-color: rgba(13, 110, 253, 0.45);
            box-shadow: 0 8px 28px rgba(13, 110, 253, 0.15);
        }

        .qac-daily-pass::before {
            background: rgba(13, 110, 253, 0.05);
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
        .qac-icon-blue   { background: rgba(13, 110, 253, 0.18); color: #60a5fa; }

        .qac-text-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 3px;
            margin-right: 15px;
        }

        .qac-text-title {
            font-size: 1rem;
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
            body.page-dashboard .container-fluid {
                padding-left: 0.85rem !important;
                padding-right: 0.85rem !important;
            }

            .ds { padding: 0.5rem 0 1rem; }

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
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }

        /* =============================================
           RESPONSIVE — Mobile (max-width: 640px)
           ============================================= */
        @media (max-width: 640px) {
            body.page-dashboard .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            .ds { padding: 0.5rem 0 1.5rem; }

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
    @endphp

    <div class="ds">
        <div class="ds-header">
            <div>
                <div class="ds-section-label">Arena Gym · Executive Dashboard</div>
                <h1 class="dashboard-title">Arena Dashboard</h1>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 bg-success text-white rounded-3 mb-4 py-2 small shadow-sm" style="background-color: #198754 !important;">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger text-white rounded-3 mb-4 py-2 small shadow-sm" style="background-color: #dc3545 !important;">
                <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
            </div>
        @endif

        @if(session('whatsapp_dispatch'))
            @php
                $dispatch = session('whatsapp_dispatch');
                $recipient = $dispatch['recipients'][0] ?? null;
            @endphp
            @if($recipient)
                <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: flex; align-items: start; justify-content: space-between; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">
                                @if($recipient['delivery_status'] === 'sent')
                                    <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Pesan Berhasil Dikirim</span>
                                @elseif($recipient['delivery_status'] === 'pending')
                                    <span style="color: #f59e0b;"><i class="fas fa-info-circle"></i> Siap Dikirim Manual</span>
                                @else
                                    <span style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Gagal Dikirim</span>
                                @endif
                            </h3>
                            <p style="margin-bottom: 0; font-size: 0.9rem;">
                                <strong>Member:</strong> {{ $recipient['name'] }}<br>
                                <strong>Nomor:</strong> {{ $recipient['phone'] ?: 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>

                    <div style="background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem; font-family: monospace; font-size: 0.85rem; white-space: pre-wrap; word-wrap: break-word; color: rgba(255,255,255,0.8);">{{ $dispatch['message'] }}</div>

                    @if($recipient['delivery_status'] === 'pending' && $recipient['url'])
                        <div style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); padding: 1rem; border-radius: 0.75rem;">
                            <div style="margin-bottom: 1rem; font-size: 0.9rem;">Pengiriman otomatis tidak berhasil. Silakan kirim manual melalui WhatsApp:</div>
                            <a href="{{ $recipient['url'] }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #25d366; color: white; padding: 0.5rem 1rem; border-radius: 999px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                <i class="fab fa-whatsapp"></i> Buka WhatsApp
                            </a>
                        </div>
                    @elseif($recipient['delivery_status'] === 'failed' && !$recipient['url'])
                        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); padding: 1rem; border-radius: 0.75rem; color: #fca5a5;">
                            <i class="fas fa-times-circle"></i> {{ $recipient['delivery_error'] ?: 'Tidak dapat mengirim pesan' }}
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <div class="section-divider">
            <span>Ringkasan Statistik</span>
        </div>

        <div class="stats-grid">
            @foreach ($stats as $stat)
                <div class="stat-card">
                    <div class="stat-label">{{ $stat['label'] }}</div>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                    <div class="stat-note">{{ $stat['note'] }}</div>
                </div>
            @endforeach
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

            {{-- Tabel Member Perlu Peringatan --}}
            <div class="box">
                <div class="box-head">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-bell text-warning"></i>
                        <span class="box-title">Member Perlu Notifikasi</span>
                    </div>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Masa Aktif</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expiringMembers as $member)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $member->full_name }}</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.45);">{{ $member->phone }}</div>
                                </td>
                                <td>
                                    <div class="expires-val">{{ $member->expires_at }}</div>
                                    <div style="display:inline-flex; margin-top:4px; font-size:10px; padding:3px 8px; border-radius:999px; background:rgba(245,158,11,0.18); color:#fbbf24; font-weight:700;">
                                        {{ $member->days_left === 0 ? 'Hari ini' : 'H-' . $member->days_left }}
                                    </div>
                                    <div style="font-size: 10px; color: rgba(255,255,255,0.42); margin-top:4px;">
                                        Diingatkan: {{ $member->last_reminder }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.announcements.reminders.send') }}">
                                        @csrf
                                        <input type="hidden" name="gym_member_id" value="{{ $member->id }}">
                                        <button type="submit" class="btn {{ $member->last_reminder !== 'Belum' ? 'btn-success' : 'btn-danger' }} btn-sm rounded-pill fw-bold"
                                            onclick="return confirm('Kirim pengingat perpanjangan untuk {{ $member->full_name }}?')">
                                            Ingatkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4" style="color: rgba(255,255,255,0.45);">
                                    Tidak ada member yang perlu diberi peringatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
