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
            /* Jarak bawah sedikit lebih lebar */
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

        .dashboard-title { font-size: 2.2rem; font-weight: 700; color: #fff; margin: 0; }

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

        .section-divider::after {
            content: "";
            height: 1px;
            width: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.1) 100%);
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
        }

        .tbl th {
            font-size: 10px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.3);
            padding: 1.2rem 1.5rem 0.8rem;
            text-align: left;
        }

        .tbl td {
            font-size: 13px;
            padding: 1.1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.85);
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

        /* =============================================
           RESPONSIVE — Tablet (max-width: 1024px)
           ============================================= */
        @media (max-width: 1024px) {
            .ds {
                padding: 0.5rem 1.2rem 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .dashboard-title {
                font-size: 1.8rem;
            }
        }

        /* =============================================
           RESPONSIVE — Mobile (max-width: 640px)
           ============================================= */
        @media (max-width: 640px) {
            .ds {
                padding: 0.5rem 0.85rem 1.5rem;
            }

            .ds-header {
                margin-bottom: 1.25rem;
                padding-bottom: 1rem;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }

            .ds-section-label {
                font-size: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .stat-card {
                border-radius: 14px;
                padding: 0.9rem 1rem;
            }

            .stat-label {
                font-size: 10px;
            }

            .stat-value {
                font-size: 1.3rem;
            }

            .stat-note {
                font-size: 10px;
            }

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

            .box {
                border-radius: 14px;
            }

            .box-head {
                padding: 1rem 1.1rem;
            }

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
        }

        /* =============================================
           RESPONSIVE — Very small (max-width: 380px)
           ============================================= */
        @media (max-width: 380px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            .stat-card {
                padding: 0.75rem 0.8rem;
            }

            .stat-value {
                font-size: 1.15rem;
            }

            .tbl th,
            .tbl td {
                padding-left: 0.85rem;
                padding-right: 0.85rem;
            }
        }
    </style>

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

            {{-- Card 3: Check-in Hari Ini (PENTING: Ambil dari heroSummary[0]) --}}
            <div class="stat-card">
                <div class="stat-label">{{ $heroSummary[0]['label'] }}</div>
                <div class="stat-value">{{ $heroSummary[0]['value'] }}</div>
                <div class="stat-note">{{ $heroSummary[0]['note'] }}</div>
            </div>

            {{-- Card 4: Membership Alert (PENTING: Ambil dari heroSummary[1]) --}}
            <div class="stat-card">
                <div class="stat-label">{{ $heroSummary[1]['label'] }}</div>
                <div class="stat-value text-warning">{{ $heroSummary[1]['value'] }}</div>
                <div class="stat-note">{{ $heroSummary[1]['note'] }}</div>
            </div>
        </div>

        <div class="section-divider">
            <span>Aktivitas Terbaru</span>
        </div>

        <div class="content-grid">
            {{-- Tabel Member Baru --}}
            <div class="box">
                <div class="box-head">
                    <span class="box-title"><i class="fas fa-user-plus me-2 text-danger"></i>Member Baru</span>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Paket</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMembers as $member)
                            <tr>
                                <td class="fw-bold">{{ $member->full_name }}</td>
                                <td style="opacity: 0.6;">{{ $member->membership_plan ?? 'Reguler' }}</td>
                                <td style="opacity: 0.5;">{{ $member->created_at->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">Belum ada member baru</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tabel Check-in Terbaru (Gabungan Member & Guest) --}}
            <div class="box">
                <div class="box-head">
                    <span class="box-title"><i class="fas fa-history me-2 text-warning"></i>Check-in Terbaru</span>
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
                        @forelse ($recentCheckins as $checkin)
                            <tr>
                                <td class="fw-bold">{{ $checkin['nama'] }}</td>
                                <td>
                                    <span
                                        class="type-pill {{ $checkin['tipe'] == 'Member' ? 'type-member' : 'type-guest' }}">
                                        {{ $checkin['tipe'] }}
                                    </span>
                                </td>
                                <td style="opacity: 0.5;">{{ $checkin['waktu'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">Belum ada aktivitas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
