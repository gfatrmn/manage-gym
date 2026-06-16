<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Riwayat Latihan | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { 'brand-red': '#ff5540' }, fontFamily: { display: ['Oswald'], mono: ['JetBrains Mono'], body: ['Hanken Grotesk'] } } } };
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; line-height: 1; }
        .glass-panel { background: linear-gradient(135deg, rgba(31,31,31,.76), rgba(14,14,14,.54)); border: 1px solid rgba(255,255,255,.13); box-shadow: 0 24px 80px rgba(0,0,0,.45); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); }
        .glass-tile { background: rgba(255,255,255,.055); border: 1px solid rgba(255,255,255,.11); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); transition: border-color .25s ease, background .25s ease, transform .25s ease; }
        .glass-tile:hover { background: rgba(255,255,255,.085); border-color: rgba(255,85,64,.52); transform: translateY(-2px); }
        .metal-grid { background-image: linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px); background-size: 48px 48px; }
        .nav-link { position: relative; padding-bottom: 6px; }
        .nav-link::after { content: ''; position: absolute; left: 0; bottom: 0; width: 0; height: 2px; background: #ff5540; transition: width .25s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .btn-primary { background: #ff5540; color: #080808; display: inline-flex; align-items: center; justify-content: center; gap: .65rem; padding: 1rem 1.35rem; font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; transition: all .25s ease; }
        .btn-primary:hover { background: #fff; color: #ff5540; transform: translateY(-2px); }
    </style>
    <link href="{{ asset('css/member-responsive.css') }}?v={{ file_exists(public_path('css/member-responsive.css')) ? filemtime(public_path('css/member-responsive.css')) : time() }}" rel="stylesheet"/>
</head>
<body class="bg-[#131313] text-[#e2e2e2] font-body selection:bg-brand-red selection:text-black">
@include('member.partials.page-loader')
<header class="fixed top-0 w-full z-50 bg-black/55 backdrop-blur-xl border-b border-white/10">
    <div class="grid grid-cols-[1fr_auto_1fr] items-center h-20 px-5 md:px-16 w-full max-w-screen-2xl mx-auto">
        <a class="justify-self-start inline-flex items-center gap-3" href="{{ route('dashboard') }}" aria-label="Arena Fitness Home">
            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="display:block;width:3.8rem;max-width:3.8rem;height:3rem;max-height:3rem;object-fit:contain;border-radius:1rem;box-shadow:0 18px 32px rgba(255,59,59,.18);background:rgba(255,255,255,.04);flex:0 0 auto;">
            <span class="font-display text-white uppercase italic text-2xl leading-none hidden sm:inline">Arena <span class="text-brand-red">Fitness</span></span>
        </a>
        <nav class="hidden lg:flex items-center justify-center gap-8 justify-self-center font-mono text-sm tracking-[0.1em]">
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('dashboard') }}">Home</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('barcode') }}">QR Code</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('membership') }}">Membership</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('profile') }}">Profil</a>
            <a class="nav-link active text-brand-red" href="{{ route('history') }}">Riwayat</a>
        </nav>
        <div class="flex items-center gap-3 justify-self-end">
            @include('member.partials.notifications')
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 font-mono text-xs border border-white/10 bg-white/5 text-white px-4 py-3 uppercase tracking-[0.18em] hover:border-brand-red hover:text-brand-red transition-colors"><span class="material-symbols-outlined text-[16px]">logout</span>Logout</button>
            </form>
        </div>
    </div>
</header>

<main class="pt-20">
    <section class="relative overflow-hidden metal-grid border-b border-white/10">
        <div class="absolute inset-0">
            <img alt="Arena Fitness workout history" class="h-full w-full object-cover grayscale brightness-[0.35] contrast-125" src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=2200&q=85" onerror="this.style.display='none'"/>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/85 to-black/35"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#131313] via-transparent to-transparent"></div>
        </div>

        <div class="relative max-w-screen-2xl mx-auto px-5 md:px-16 py-10 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-7 flex flex-col justify-center">
                    <div class="inline-flex w-fit items-center gap-3 glass-tile px-4 py-2 mb-6">
                        <span class="h-2 w-2 bg-brand-red shadow-[0_0_14px_#ff5540]"></span>
                        <span class="font-mono text-[11px] uppercase tracking-[0.28em] text-[#ffb4a8]">Training Log</span>
                    </div>
                    <h1 class="font-display text-[52px] md:text-[84px] uppercase italic leading-[0.92] mb-6 text-white">Riwayat<br/><span class="text-brand-red">Latihan</span></h1>
                    <p class="text-[#ebbbb4] text-lg md:text-xl max-w-2xl leading-relaxed">Lihat berapa kali Anda datang latihan, kapan terakhir check-in, dan seberapa rutin latihan selama membership aktif.</p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a class="btn-primary" href="{{ route('barcode') }}"><span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>Check-In Sekarang</a>
                        <a class="glass-tile inline-flex items-center justify-center gap-3 px-5 py-4 font-mono text-xs uppercase tracking-[0.18em] text-white hover:text-brand-red" href="{{ route('membership') }}"><span class="material-symbols-outlined text-[18px]">workspace_premium</span>Membership</a>
                    </div>
                </div>
                <div class="lg:col-span-5 glass-panel p-6 md:p-8 flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-4 mb-8">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.24em] text-[#ebbbb4]">Nilai Kerajinan Latihan</p>
                            <h2 class="font-display text-4xl uppercase italic text-white mt-2">{{ $trainingAssessment ?? '-' }}</h2>
                        </div>
                        <span class="inline-flex h-12 w-12 items-center justify-center border border-brand-red/40 bg-brand-red/10 text-brand-red"><span class="material-symbols-outlined">local_fire_department</span></span>
                    </div>
                    <div>
                        <p class="font-display text-7xl text-brand-red leading-none">{{ $trainingConsistency ?? 0 }}%</p>
                        <p class="text-[#ebbbb4] mt-3">Ada latihan di {{ $activeTrainingDays ?? 0 }} dari {{ $trackedTrainingDays ?? 0 }} hari terakhir.</p>
                        <div class="mt-5 h-3 bg-white/10 overflow-hidden"><div class="h-full bg-brand-red" style="width: {{ min(100, max(0, $trainingConsistency ?? 0)) }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 md:px-16 py-10 bg-[#131313]">
        <div class="max-w-screen-2xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="glass-tile p-5">
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Total Latihan</p>
                <p class="font-display text-5xl text-white mt-2">{{ $totalCheckins ?? 0 }}</p>
                <p class="text-sm text-[#ebbbb4] mt-2">Jumlah semua kunjungan ke gym.</p>
            </div>
            <div class="glass-tile p-5">
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Latihan Bulan Ini</p>
                <p class="font-display text-5xl text-white mt-2">{{ $thisMonthCheckins ?? 0 }}</p>
                <p class="text-sm text-[#ebbbb4] mt-2">Kunjungan dari awal bulan.</p>
            </div>
            <div class="glass-tile p-5">
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Latihan Minggu Ini</p>
                <p class="font-display text-5xl text-brand-red mt-2">{{ $thisWeekCheckins ?? 0 }}</p>
                <p class="text-sm text-[#ebbbb4] mt-2">Kunjungan minggu ini.</p>
            </div>
        </div>
    </section>

    <section class="px-5 md:px-16 py-12 bg-[#0e0e0e] border-y border-white/10">
        <div class="max-w-screen-2xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 glass-panel p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">30 Hari Terakhir</span>
                        <h2 class="font-display text-4xl uppercase italic text-white mt-2">Grafik Datang Latihan</h2>
                    </div>
                    <span class="font-mono text-xs uppercase tracking-[0.18em] border border-brand-red/40 bg-brand-red/10 text-brand-red px-3 py-2">{{ $trainingAssessment ?? '-' }}</span>
                </div>
                <div class="h-[220px]"><canvas id="trainingConsistencyChart"></canvas></div>
            </div>
            <div class="lg:col-span-4 glass-panel p-6 md:p-8">
                <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">Penjelasan Singkat</span>
                <h3 class="font-display text-4xl uppercase italic text-white mt-2 mb-4">Cara Membaca</h3>
                <p class="text-[#ebbbb4] leading-relaxed">Satu batang di grafik berarti jumlah check-in pada tanggal itu. Jika banyak tanggal terisi, berarti latihan Anda lebih rutin.</p>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="glass-tile p-4"><p class="font-mono text-[10px] uppercase text-[#ebbbb4]">Hari Latihan</p><p class="font-display text-3xl text-white mt-1">{{ $activeTrainingDays ?? 0 }}</p></div>
                    <div class="glass-tile p-4"><p class="font-mono text-[10px] uppercase text-[#ebbbb4]">Total Hari</p><p class="font-display text-3xl text-white mt-1">{{ $trackedTrainingDays ?? 0 }}</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 md:px-16 py-12 md:py-16 bg-[#131313]">
        <div class="max-w-screen-2xl mx-auto glass-panel overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 border-b border-white/10">
                <div>
                    <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">Verified Check-In</span>
                    <h2 class="font-display text-3xl uppercase italic text-white mt-2">Log Aktivitas</h2>
                </div>
                <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Arena Fitness Gate</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px]">
                    <thead>
                    <tr class="border-b border-white/10 bg-white/5 font-mono text-[10px] uppercase tracking-[0.16em] text-[#ebbbb4]">
                        <th class="text-left px-6 py-4">Tanggal</th>
                        <th class="text-left px-6 py-4">Waktu</th>
                        <th class="text-left px-6 py-4">Metode</th>
                        <th class="text-left px-6 py-4">Status</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                    @forelse(($checkins ?? collect()) as $checkin)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-5 text-white">{{ optional($checkin->checked_in_at)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-5 text-[#ebbbb4]">{{ optional($checkin->checked_in_at)->format('H:i') }}</td>
                            <td class="px-6 py-5 uppercase text-white">{{ str_replace('_', ' ', $checkin->checkin_method ?? '-') }}</td>
                            <td class="px-6 py-5"><span class="inline-flex items-center gap-2 border border-green-500/20 bg-green-500/10 px-3 py-1 font-mono text-[10px] uppercase tracking-[0.14em] text-green-300"><span class="material-symbols-outlined text-[14px]">verified</span>Terverifikasi</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-[#ebbbb4]">Belum ada riwayat latihan. Riwayat akan muncul setelah scan barcode saat check-in.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($checkins) && method_exists($checkins, 'links'))
            <div class="max-w-screen-2xl mx-auto mt-6">{{ $checkins->links() }}</div>
        @endif
    </section>
</main>

<script>
    const trainingLabels = @json($trainingChartLabels ?? []);
    const trainingValues = @json($trainingChartValues ?? []);
    const trainingCanvas = document.getElementById('trainingConsistencyChart');

    if (trainingCanvas) {
        new Chart(trainingCanvas, {
            type: 'bar',
            data: { labels: trainingLabels, datasets: [{ label: 'Check-in', data: trainingValues, backgroundColor: 'rgba(255, 85, 64, 0.78)', borderColor: '#ffb4a8', borderWidth: 1, borderRadius: 5, maxBarThickness: 22 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#111', borderColor: 'rgba(255,85,64,.45)', borderWidth: 1, titleColor: '#fff', bodyColor: '#ebbbb4' } }, scales: { x: { ticks: { color: '#b8aaa6', maxRotation: 0, autoSkip: true, maxTicksLimit: 10 }, grid: { color: 'rgba(255,255,255,0.05)' } }, y: { beginAtZero: true, ticks: { color: '#b8aaa6', precision: 0 }, grid: { color: 'rgba(255,255,255,0.08)' } } } }
        });
    }
</script>
</body>
</html>
