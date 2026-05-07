@extends('admin.layout')

@section('content')
    <style>
        .admin-dashboard-shell {
            position: relative;
            z-index: 1;
            max-width: 1320px;
            width: 100%;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 3rem;
            color: #fff;
        }

        .admin-dashboard-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 2rem;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.03);
            pointer-events: none;
        }

        .admin-dashboard-shell .hero-panel {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .admin-dashboard-shell .hero-card,
        .admin-dashboard-shell .stats-container .card,
        .admin-dashboard-shell .section-box,
        .admin-dashboard-shell .target-box {
            border-radius: 28px;
            backdrop-filter: blur(18px);
            background: rgba(11, 12, 18, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
        }

        .admin-dashboard-shell .hero-card {
            display: grid;
            gap: 1.75rem;
            padding: 2rem;
            min-height: 360px;
        }

        .admin-dashboard-shell .hero-card .hero-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
        }

        .admin-dashboard-shell .hero-card .hero-header .hero-info {
            max-width: 60rem;
        }

        .admin-dashboard-shell .hero-card .hero-header .hero-info .section-label {
            text-transform: uppercase;
            letter-spacing: .2em;
            color: rgba(255,255,255,0.56);
            font-size: .78rem;
            margin-bottom: .85rem;
        }

        .admin-dashboard-shell .hero-card .hero-header h1 {
            margin: 0;
            font-size: clamp(2.4rem, 3.8vw, 3.8rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .admin-dashboard-shell .hero-card .hero-header p {
            color: rgba(255,255,255,0.74);
            font-size: 1.05rem;
            max-width: 55rem;
            line-height: 1.85;
            margin: 1rem 0 0;
        }

        .admin-dashboard-shell .hero-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .admin-dashboard-shell .hero-summary-card {
            padding: 1.4rem 1.6rem;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
            min-height: 130px;
        }

        .admin-dashboard-shell .hero-summary-card h4 {
            margin: 0 0 .85rem;
            color: rgba(255,255,255,0.72);
            font-size: .82rem;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .admin-dashboard-shell .hero-summary-card .summary-value {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .admin-dashboard-shell .hero-summary-card .summary-note {
            margin-top: .85rem;
            color: rgba(255,255,255,0.62);
            font-size: .92rem;
            line-height: 1.6;
        }

        .admin-dashboard-shell .hero-image {
            position: relative;
            min-height: 360px;
            overflow: hidden;
            border-radius: 28px;
        }

        .admin-dashboard-shell .hero-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.15), rgba(0,0,0,0.65));
            z-index: 1;
        }

        .admin-dashboard-shell .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .admin-dashboard-shell .hero-image .hero-profile {
            position: absolute;
            bottom: 1.5rem;
            left: 1.5rem;
            right: 1.5rem;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.3rem;
            background: rgba(5, 7, 11, 0.72);
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .admin-dashboard-shell .hero-image .hero-profile img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,0.12);
        }

        .admin-dashboard-shell .hero-image .hero-profile .profile-info {
            display: flex;
            flex-direction: column;
            gap: .15rem;
        }

        .admin-dashboard-shell .hero-image .hero-profile .profile-info .name {
            font-weight: 700;
            color: #fff;
        }

        .admin-dashboard-shell .hero-image .hero-profile .profile-info .role {
            color: rgba(255,255,255,0.68);
            font-size: .92rem;
        }

        .admin-dashboard-shell .stats-container {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .admin-dashboard-shell .card {
            position: relative;
            padding: 1.8rem 1.8rem 1.6rem;
            overflow: hidden;
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .admin-dashboard-shell .card:hover {
            transform: translateY(-6px);
            border-color: rgba(255,49,49,0.24);
            box-shadow: 0 18px 40px rgba(255, 49, 49, 0.12);
        }

        .admin-dashboard-shell .card-bg-icon {
            position: absolute;
            right: 1.2rem;
            bottom: 1.2rem;
            font-size: 3.8rem;
            color: rgba(255,255,255,0.08);
        }

        .admin-dashboard-shell .card h3 {
            margin: 0 0 .8rem;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: rgba(255,255,255,0.68);
        }

        .admin-dashboard-shell .card .value {
            font-size: 1.95rem;
            font-weight: 700;
            margin-bottom: .9rem;
        }

        .admin-dashboard-shell .indicator {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .65rem 1rem;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 700;
        }

        .admin-dashboard-shell .plus {
            background: rgba(0, 255, 133, 0.12);
            color: #00ff85;
        }

        .admin-dashboard-shell .neutral {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .admin-dashboard-shell .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        .admin-dashboard-shell .section-box {
            padding: 1.9rem;
        }

        .admin-dashboard-shell .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .admin-dashboard-shell .section-title h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        .admin-dashboard-shell .btn-action {
            background: #ff3131;
            color: #fff;
            border: none;
            padding: 0.82rem 1.35rem;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .admin-dashboard-shell .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(255, 49, 49, 0.18);
        }

        .admin-dashboard-shell .member-list {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-dashboard-shell .member-list th,
        .admin-dashboard-shell .member-list td {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .admin-dashboard-shell .member-list th {
            color: rgba(255,255,255,0.64);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .16em;
        }

        .admin-dashboard-shell .member-list td {
            color: rgba(255,255,255,0.88);
            font-size: .95rem;
        }

        .admin-dashboard-shell .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .55rem .95rem;
            border-radius: 999px;
            font-size: .75rem;
            background: rgba(255, 49, 49, 0.12);
            color: #ff3131;
            font-weight: 700;
        }

        .admin-dashboard-shell .target-box {
            position: relative;
            min-height: 100%;
            background: linear-gradient(135deg, rgba(255,49,49,0.18), rgba(0,0,0,0.5));
            overflow: hidden;
        }

        .admin-dashboard-shell .target-box::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=900') center/cover no-repeat;
            opacity: .18;
            filter: brightness(0.65);
        }

        .admin-dashboard-shell .target-content {
            position: relative;
            z-index: 1;
            padding: 1.9rem;
            color: #fff;
        }

        .admin-dashboard-shell .target-content h3 {
            margin: 0 0 .9rem;
            font-size: 1.3rem;
        }

        .admin-dashboard-shell .target-content p {
            margin: 0;
            color: rgba(255,255,255,0.82);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .admin-dashboard-shell .progress-bar {
            height: 12px;
            background: rgba(255,255,255,0.14);
            border-radius: 999px;
            overflow: hidden;
        }

        .admin-dashboard-shell .progress-bar-inner {
            width: 85%;
            height: 100%;
            background: #ff3131;
            border-radius: 999px;
            box-shadow: 0 0 18px rgba(255,49,49,0.3);
        }

        @media (max-width: 1200px) {
            .admin-dashboard-shell .hero-panel,
            .admin-dashboard-shell .content-grid {
                grid-template-columns: 1fr;
            }

            .admin-dashboard-shell .stats-container {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .admin-dashboard-shell .hero-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-dashboard-shell {
                padding: 1.5rem 1rem 2rem;
            }

            .admin-dashboard-shell .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="admin-dashboard-shell">
        <div class="hero-panel">
            <div class="hero-card">
                <div class="hero-header">
                    <div class="hero-info">
                        <div class="section-label">Arena Gym Executive Dashboard</div>
                        <h1>Arena Dashboard</h1>
                        <p>Pantau pertumbuhan statistik member dan finansial hari ini, lihat laporan pemasukan, dan kelola aktivitas operasional secara real-time.</p>
                    </div>
                </div>

                @php
                    $heroSummaryText = is_array($heroSummary) ? null : $heroSummary;
                @endphp

                <p class="hero-copy">{{ $heroSummaryText ?? 'Pantau pertumbuhan statistik member dan finansial hari ini, lihat laporan pemasukan, dan kelola aktivitas operasional secara real-time.' }}</p>

                @if (is_array($heroSummary) && count($heroSummary))
                    <div class="hero-summary-grid">
                        @foreach ($heroSummary as $summary)
                            <div class="hero-summary-card">
                                <h4>{{ $summary['label'] ?? 'Ringkasan' }}</h4>
                                <div class="summary-value">{{ $summary['value'] ?? '-' }}</div>
                                <div class="summary-note">{{ $summary['note'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Gym Background">
                <div class="hero-profile">
                    <div style="display:flex; align-items:center; gap:.9rem;">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" alt="Admin">
                        <div class="profile-info">
                            <div class="name">Admin Arena</div>
                            <div class="role">Manajemen Operasional</div>
                        </div>
                    </div>
                    <div style="color: rgba(255,255,255,0.75); font-size:.88rem;">Selamat datang kembali.</div>
                </div>
            </div>
        </div>

        <div class="stats-container">
            @foreach ($stats as $index => $stat)
                <div class="card">
                    <div class="card-bg-icon">
                        @if ($index === 0)
                            <i class="fas fa-users"></i>
                        @elseif ($index === 1)
                            <i class="fas fa-wallet"></i>
                        @elseif ($index === 2)
                            <i class="fas fa-receipt"></i>
                        @else
                            <i class="fas fa-crown"></i>
                        @endif
                    </div>
                    <h3>{{ $stat['label'] }}</h3>
                    <span class="value">{{ $stat['value'] }}</span>
                    <span class="indicator {{ $index === 2 ? 'neutral' : 'plus' }}">
                        @if ($index === 2)
                            Stable
                        @else
                            <i class="fas fa-caret-up"></i> {{ $index === 0 ? '12% Month' : ($index === 1 ? '8% Day' : '5% Day') }}
                        @endif
                    </span>
                </div>
            @endforeach
        </div>

        <div class="content-grid">
            <div class="section-box">
                <div class="section-title">
                    <h2>Member Baru Terdaftar</h2>
                    <button class="btn-action">Lihat Semua</button>
                </div>
                <table class="member-list">
                    <thead>
                        <tr>
                            <th>Nama Member</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMembers as $member)
                            <tr>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ $member->membership_plan ?? ucfirst($member->member_status ?? 'member') }}</td>
                                <td><span class="status-pill">{{ $member->member_status === 'member' ? 'Member' : 'Non Member' }}</span></td>
                                <td>{{ optional($member->created_at)->format('H:i A') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color: rgba(255,255,255,0.65);">Belum ada member baru yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="section-box target-box">
                <div class="target-content">
                    <h3>Target Bulanan</h3>
                    <p>Analisis kinerja pendapatan dan target pendapatan bulanan untuk terus semangat.</p>
                    <div class="progress-bar">
                        <div class="progress-bar-inner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
