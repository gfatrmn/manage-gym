@extends('admin.layout')

@section('content')
<style>
    .ds {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0.5rem 1.5rem 1rem;
        color: var(--color-text-primary, #fff);
        font-family: var(--font-sans, system-ui, sans-serif);
    }

    /* ── Header ─────────────────────────────────────── */
    .ds-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.1rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .ds-section-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .16em;
        color: rgba(255,255,255,0.45);
        margin-bottom: .3rem;
    }
    .ds-title {
        font-size: clamp(1.6rem, 2.8vw, 2rem);
        font-weight: 600;
        margin: 0 0 .3rem;
        letter-spacing: -.02em;
    }
    .ds-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,0.55);
        line-height: 1.65;
        max-width: 560px;
        margin: 0;
    }
    .ds-profile {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: .65rem 1rem;
        flex-shrink: 0;
    }
    .ds-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        flex-shrink: 0;
    }
    .ds-pname  { font-size: 13px; font-weight: 600; color: #fff; }
    .ds-prole  { font-size: 11px; color: rgba(255,255,255,0.52); }

    /* ── Stat cards ───────────────────────────────────── */
    .stats-grid {
        display: grid;
        /* Paksa menjadi 4 kolom sejajar */
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 1.1rem;
    }
    .stat-card, .sum-card {
        /* Pastikan semua kartu memiliki tinggi yang sama agar rapi */
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 1.1rem 1.2rem;
        display: flex;
        flex-direction: column;
        gap: .3rem;
        transition: border-color .2s, transform .2s;
        /* Pastikan lebar minimum terjaga */
        width: 100%;
    }
    .stat-card:hover {
        border-color: rgba(255,49,49,.22);
        transform: translateY(-3px);
    }
    .stat-icon { font-size: 18px; color: rgba(255,255,255,0.35); margin-bottom: .15rem; }
    .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: .13em; color: rgba(255,255,255,0.45); }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.15; }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        align-self: flex-start;
        margin-top: .25rem;
    }
    .badge-up      { background: rgba(0,255,133,.12); color: #00e87a; }
    .badge-neutral { background: rgba(255,255,255,.08); color: rgba(255,255,255,.7); }

    /* ── Hero summary strip ───────────────────────────── */
    .hero-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        margin-bottom: 1.25rem;
    }
    .sum-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
        padding: .9rem 1.1rem;
    }
    .sum-label { font-size: 11px; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,0.42); margin-bottom: .4rem; }
    .sum-value { font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: .2rem; }
    .sum-note  { font-size: 12px; color: rgba(255,255,255,0.52); line-height: 1.5; }

    /* ── Content grid ────────────────────────────────── */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr; /* Menjadi satu kolom penuh */
        gap: 1.1rem;
        align-items: start;
    }
    .box {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        overflow: hidden;
        width: 100%; /* Pastikan box mengambil lebar penuh */
    }
    .box-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .95rem 1.25rem .8rem;
        border-bottom: 1px solid rgba(255,255,255,0.07);
    }
    .box-title { font-size: 14px; font-weight: 600; color: #fff; }
    .btn-sm {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        background: transparent;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 999px;
        padding: 4px 13px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-sm:hover { background: rgba(255,255,255,0.07); }

    /* ── Member table ─────────────────────────────────── */
    .tbl {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Opsional: Membuat lebar kolom lebih konsisten */
    }
    .tbl th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .13em;
        color: rgba(255,255,255,0.38);
        padding: .6rem 1.25rem;
        text-align: left;
        font-weight: 400;
    }
    .tbl td {
        font-size: 13px;
        padding: .75rem 1.25rem;
        border-top: 1px solid rgba(255,255,255,0.06);
        color: rgba(255,255,255,0.85);
    }
    .pill {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 999px;
        font-weight: 700;
        background: rgba(255,49,49,0.14);
        color: #ff6060;
    }
    .pill-ok { background: rgba(0,255,133,0.12); color: #00e87a; }

    /* ── Target / progress box ────────────────────────── */
    .target-inner { padding: 1.1rem 1.25rem; display: flex; flex-direction: column; gap: .85rem; }
    .divider { height: 1px; background: rgba(255,255,255,0.07); }
    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: rgba(255,255,255,0.5);
        margin-bottom: .45rem;
    }
    .progress-label strong { color: rgba(255,255,255,0.85); font-weight: 600; }
    .progress-track {
        height: 6px;
        background: rgba(255,255,255,0.1);
        border-radius: 999px;
        overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 999px; }
    .fill-red  { background: #ff3131; }
    .fill-blue { background: #3b9eff; }
    .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }
    .meta-key { color: rgba(255,255,255,0.5); }
    .meta-val { font-weight: 600; color: #fff; }
    .meta-val.green { color: #00e87a; }
    .meta-val.blue  { color: #3b9eff; }

    /* ── Responsive ──────────────────────────────────── */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .ds { padding: 1.25rem 1rem 2rem; }
        .ds-header { flex-direction: column; }
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="ds">

    {{-- ── Header ── --}}
    <div class="ds-header">
        <div>
            <div class="ds-section-label">Arena Gym · Executive Dashboard</div>
            <h1 class="ds-title">Arena Dashboard</h1>
        </div>
        <div class="ds-profile">
            <div class="ds-avatar">AA</div>
            <div>
                <div class="ds-pname">Admin Arena</div>
                <div class="ds-prole">Manajemen Operasional</div>
            </div>
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="stats-grid">
        @foreach (array_slice($stats, 0, 2) as $index => $stat)
        <div class="stat-card">
            <div class="stat-label">{{ $stat['label'] }}</div>
            <div class="stat-value">{{ $stat['value'] }}</div>
            <span class="stat-badge badge-up">
                <i class="fas fa-caret-up"></i>
                {{ $index === 0 ? '+12%' : '+8%' }}
            </span>
        </div>
        @endforeach

        {{-- 2 Card Ringkasan (Check-in & Membership Alert) --}}
        @foreach (array_slice($heroSummary, 0, 2) as $summary)
        <div class="stat-card"> {{-- Gunakan class stat-card agar desainnya seragam --}}
            <div class="stat-label">{{ $summary['label'] }}</div>
            <div class="stat-value" style="font-size: 1.5rem;">{{ $summary['value'] }}</div>
            <div class="small mt-1" style="color: rgba(255,255,255,0.5); font-size: 11px;">
                {{ $summary['note'] }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Content grid ── --}}
    <div class="content-grid">

        {{-- Member table --}}
        <div class="box">
            <div class="box-head">
                <span class="box-title">Member baru terdaftar</span>
                <button class="btn-sm">Lihat semua</button>
            </div>
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Paket</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentMembers as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td style="color:rgba(255,255,255,0.55);">
                        {{ $member->membership_plan ?? 'Reguler' }}
                        </td>
                        <td>
                            {{-- Karena sekarang ini tabel khusus member, statusnya kemungkinan 'active' --}}
                            <span class="pill {{ $member->status === 'active' ? 'pill-ok' : '' }}">
                                {{ $member->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td style="color:rgba(255,255,255,0.5);">{{ optional($member->created_at)->format('H:i') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:rgba(255,255,255,0.38); padding: 2rem 1.25rem;">
                            Belum ada member baru yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
