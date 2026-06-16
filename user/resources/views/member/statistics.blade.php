<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Statistik Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-[#131313] text-white">
@include('member.partials.page-loader')
<header class="border-b border-white/10 bg-[#131313]">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="font-bold uppercase tracking-widest text-[#ff5540]">Arena Fitness</a>
        <div class="flex items-center gap-3">
            @include('member.partials.notifications')
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs uppercase tracking-widest bg-[#ff5540] text-black px-4 py-2">Logout</button>
            </form>
        </div>
    </div>
</header>
<main class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-8">Statistik Check-in</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white/5 border border-white/10 p-5 rounded-lg">
            <p class="text-white/60 text-sm">Total Check-in</p>
            <p class="text-3xl font-bold mt-2">{{ $totalCheckins }}</p>
        </div>
        <div class="bg-white/5 border border-white/10 p-5 rounded-lg">
            <p class="text-white/60 text-sm">Check-in Bulan Ini</p>
            <p class="text-3xl font-bold mt-2">{{ $thisMonthCheckins }}</p>
        </div>
    </div>
    <div class="bg-white/5 border border-white/10 p-5 rounded-lg">
        <p class="text-white/60 text-sm">Check-in Terakhir</p>
        <p class="text-lg font-semibold mt-2">{{ $lastCheckin ? \Carbon\Carbon::parse($lastCheckin)->format('d M Y H:i') : '-' }}</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">
        <div class="bg-white/5 border border-white/10 p-5 rounded-lg">
            <p class="text-white/70 text-sm mb-3">Grafik 7 Hari Terakhir</p>
            <canvas id="weeklyChart" height="140"></canvas>
        </div>
        <div class="bg-white/5 border border-white/10 p-5 rounded-lg">
            <p class="text-white/70 text-sm mb-3">Grafik 6 Bulan Terakhir</p>
            <canvas id="monthlyChart" height="140"></canvas>
        </div>
    </div>
</main>
<script>
    const weeklyLabels = @json(collect($weeklyStats ?? [])->pluck('label'));
    const weeklyValues = @json(collect($weeklyStats ?? [])->pluck('value'));
    const monthlyLabels = @json(collect($monthlyStats ?? [])->pluck('label'));
    const monthlyValues = @json(collect($monthlyStats ?? [])->pluck('value'));

    new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Check-in',
                data: weeklyValues,
                borderColor: '#ff5540',
                backgroundColor: 'rgba(255, 85, 64, 0.25)',
                tension: 0.35,
                fill: true,
                pointRadius: 3,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#b3b3b3' }, grid: { color: 'rgba(255,255,255,0.08)' } },
                y: { ticks: { color: '#b3b3b3', precision: 0 }, grid: { color: 'rgba(255,255,255,0.08)' }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Check-in',
                data: monthlyValues,
                backgroundColor: 'rgba(255, 85, 64, 0.75)',
                borderRadius: 6,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#b3b3b3' }, grid: { color: 'rgba(255,255,255,0.08)' } },
                y: { ticks: { color: '#b3b3b3', precision: 0 }, grid: { color: 'rgba(255,255,255,0.08)' }, beginAtZero: true }
            }
        }
    });
</script>
</body>
</html>
