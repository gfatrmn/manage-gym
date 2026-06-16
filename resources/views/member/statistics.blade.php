<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>IRON ELITE | Statistik Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#131313] text-white min-h-screen">
    <header class="w-full border-b border-white/10 py-5">
        <div class="max-w-6xl mx-auto px-5 flex justify-between items-center">
            <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="width: 110px; height: auto;">
            </a>
            <form method="POST" action="{{ route('member.logout') }}">@csrf<button class="bg-red-600 text-black px-4 py-2 rounded">Logout</button></form>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-5 py-10">
        <h1 class="text-3xl font-bold mb-6">Statistik Check-in</h1>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-5 rounded-xl border border-white/10 bg-white/5"><div class="text-sm text-white/60">Total Check-in</div><div class="text-3xl font-bold mt-2">{{ $totalCheckins }}</div></div>
            <div class="p-5 rounded-xl border border-white/10 bg-white/5"><div class="text-sm text-white/60">Check-in Bulan Ini</div><div class="text-3xl font-bold mt-2">{{ $thisMonthCheckins }}</div></div>
            <div class="p-5 rounded-xl border border-white/10 bg-white/5"><div class="text-sm text-white/60">Check-in Terakhir</div><div class="text-lg font-semibold mt-2">{{ $lastCheckinAt ? $lastCheckinAt->format('d M Y H:i') : '-' }}</div></div>
        </div>
    </main>
</body>
</html>

