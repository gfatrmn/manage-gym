<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Arena Fitness | DOMINASI SETIAP LIMIT</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#ffb4a8',
                        secondary: '#c8c6c5',
                        error: '#ffb4ab',
                        background: '#131313',
                        surface: '#131313',
                        'surface-dim': '#131313',
                        'surface-container': '#1f1f1f',
                        'surface-container-low': '#1b1b1b',
                        'surface-container-high': '#2a2a2a',
                        'surface-container-highest': '#353535',
                        'surface-container-lowest': '#0e0e0e',
                        'surface-variant': '#353535',
                        'on-background': '#e2e2e2',
                        'on-surface': '#e2e2e2',
                        'on-surface-variant': '#ebbbb4',
                        'primary-container': '#ff5540',
                        'on-primary-container': '#5c0000',
                        'brand-red': '#ff5540',
                    },
                    borderRadius: {
                        DEFAULT: '0.25rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        full: '9999px',
                    },
                    spacing: {
                        'margin-desktop': '64px',
                        'margin-mobile': '20px',
                        unit: '8px',
                        'section-padding': '120px',
                        gutter: '32px',
                    },
                    fontFamily: {
                        'headline-md': ['Oswald'],
                        'body-md': ['Hanken Grotesk'],
                        'headline-lg-mobile': ['Oswald'],
                        'headline-lg': ['Oswald'],
                        'display-xl': ['Oswald'],
                        'label-caps': ['JetBrains Mono'],
                        'body-lg': ['Hanken Grotesk'],
                    },
                    fontSize: {
                        'headline-md': ['32px', {lineHeight: '38px', fontWeight: '600'}],
                        'body-md': ['16px', {lineHeight: '24px', fontWeight: '400'}],
                        'headline-lg-mobile': ['36px', {lineHeight: '40px', fontWeight: '600'}],
                        'headline-lg': ['48px', {lineHeight: '56px', fontWeight: '600'}],
                        'display-xl': ['80px', {lineHeight: '90px', letterSpacing: '0', fontWeight: '700'}],
                        'label-caps': ['14px', {lineHeight: '20px', letterSpacing: '0.1em', fontWeight: '500'}],
                        'body-lg': ['18px', {lineHeight: '28px', fontWeight: '400'}],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }

        .btn-primary {
            background-color: #ff5540;
            color: #000;
            text-transform: uppercase;
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.5rem;
        }

        .btn-primary:hover {
            background-color: #fff;
            color: #ff5540;
            box-shadow: 0 0 20px rgba(255, 85, 64, 0.4);
            transform: translateY(-2px);
        }

        .glass-panel {
            background: linear-gradient(135deg, rgba(31,31,31,.72), rgba(14,14,14,.48));
            border: 1px solid rgba(255,255,255,.14);
            box-shadow: 0 24px 80px rgba(0,0,0,.48);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .glass-tile {
            background: rgba(255,255,255,.055);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            transition: border-color .25s ease, background .25s ease, transform .25s ease;
        }

        .glass-tile:hover {
            background: rgba(255,255,255,.085);
            border-color: rgba(255,85,64,.52);
            transform: translateY(-2px);
        }

        .metal-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .hero-vignette {
            background:
                linear-gradient(90deg, rgba(0,0,0,.96) 0%, rgba(0,0,0,.78) 42%, rgba(0,0,0,.30) 100%),
                linear-gradient(0deg, #131313 0%, rgba(19,19,19,0) 42%);
        }

        .nav-link {
            position: relative;
            padding-bottom: 6px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: #ff5540;
            transition: width .25s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .scan-line {
            height: 2px;
            background: linear-gradient(to right, transparent, #ff5540, transparent);
            position: absolute;
            width: 100%;
            z-index: 20;
            animation: scan 3s linear infinite;
        }

        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }

        .bg-background { background-color: #131313; }
        .bg-surface-container { background-color: #1f1f1f; }
        .bg-surface-container-low { background-color: #1b1b1b; }
        .bg-surface-container-high { background-color: #2a2a2a; }
        .bg-surface-container-lowest { background-color: #0e0e0e; }
        .bg-surface-dim { background-color: #131313; }
        .bg-brand-red { background-color: #ff5540; }
        .border-surface-variant { border-color: #353535; }
        .text-brand-red { color: #ff5540; }
        .text-on-background, .text-on-surface { color: #e2e2e2; }
        .text-on-surface-variant { color: #ebbbb4; }
        .text-primary { color: #ffb4a8; }
        .font-body-md, .font-body-lg { font-family: 'Hanken Grotesk', sans-serif; }
        .font-label-caps { font-family: 'JetBrains Mono', monospace; }
        .font-display-xl, .font-headline-lg, .font-headline-md { font-family: 'Oswald', sans-serif; }
        .text-label-caps { font-size: 14px; line-height: 20px; letter-spacing: 0.1em; font-weight: 500; }
        .text-headline-lg { font-size: 48px; line-height: 56px; font-weight: 600; }
        .text-display-xl { font-size: 80px; line-height: 90px; letter-spacing: 0; font-weight: 700; }
        .px-margin-mobile { padding-left: 20px; padding-right: 20px; }
        .py-section-padding { padding-top: 120px; padding-bottom: 120px; }
        @media (min-width: 768px) {
            .md\:px-margin-desktop { padding-left: 64px; padding-right: 64px; }
        }
    </style>
    <link href="{{ asset('css/member-responsive.css') }}?v={{ file_exists(public_path('css/member-responsive.css')) ? filemtime(public_path('css/member-responsive.css')) : time() }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-brand-red selection:text-white">
@include('member.partials.page-loader')
@php
    $channelUrl = config('services.whatsapp.channel_url');
    $memberAnnouncements = $announcements ?? collect();
    $hasMembershipWarning = filled($membershipWarning ?? null);
    $shouldShowChannelPrompt = ($showWhatsAppChannelPrompt ?? false) && filled($channelUrl);
    $notificationCount = $memberAnnouncements->count() + ($hasMembershipWarning ? 1 : 0) + ($shouldShowChannelPrompt ? 1 : 0);
@endphp
<header class="fixed top-0 w-full z-50 bg-black/55 backdrop-blur-xl border-b border-white/10">
    <div class="grid grid-cols-[1fr_auto_1fr] items-center h-20 px-margin-mobile md:px-margin-desktop w-full max-w-screen-2xl mx-auto">
        <a class="justify-self-start inline-flex items-center gap-3" href="{{ route('dashboard') }}" aria-label="Arena Fitness Home">
            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="display:block;width:3.8rem;max-width:3.8rem;height:3rem;max-height:3rem;object-fit:contain;border-radius:1rem;box-shadow:0 18px 32px rgba(255,59,59,.18);background:rgba(255,255,255,.04);flex:0 0 auto;">
            <span class="font-display-xl text-white uppercase italic text-2xl tracking-tighter leading-none hidden sm:inline">Arena <span class="text-brand-red">Fitness</span></span>
        </a>
        <nav class="hidden lg:flex items-center justify-center gap-8 justify-self-center">
            <a class="nav-link active font-label-caps text-label-caps text-brand-red" href="{{ route('dashboard') }}">Home</a>
            <a class="nav-link font-label-caps text-label-caps text-on-surface-variant hover:text-brand-red transition-colors" href="{{ route('barcode') }}">QR Code</a>
            <a class="nav-link font-label-caps text-label-caps text-on-surface-variant hover:text-brand-red transition-colors" href="{{ route('membership') }}">Membership</a>
            <a class="nav-link font-label-caps text-label-caps text-on-surface-variant hover:text-brand-red transition-colors" href="{{ route('profile') }}">Profil</a>
            <a class="nav-link font-label-caps text-label-caps text-on-surface-variant hover:text-brand-red transition-colors" href="{{ route('history') }}">Riwayat</a>
        </nav>
        <div class="justify-self-end flex items-center gap-3 relative">
            @include('member.partials.notifications')
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 font-label-caps text-xs border border-white/10 bg-white/5 text-white px-4 py-3 uppercase tracking-[0.18em] hover:border-brand-red hover:text-brand-red transition-colors">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<main class="pt-20">
    @if(session('status'))
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop pt-4">
            <div class="border border-brand-red/30 bg-brand-red/10 text-[#ffb4a8] px-4 py-3">
                {{ session('status') }}
            </div>
        </div>
    @endif
    <section class="relative min-h-[760px] flex items-center overflow-hidden metal-grid">
        @php
            $firstName = strtoupper(explode(' ', trim($user->name))[0] ?? 'MEMBER');
            $expiresAt = $member?->expires_at;
            $remainingDays = $expiresAt ? max(0, now()->startOfDay()->diffInDays($expiresAt->copy()->startOfDay(), false)) : null;
        @endphp
        <div class="absolute inset-0 z-0">
            <img alt="Arena Fitness training floor" class="w-full h-full object-cover grayscale brightness-[0.42] contrast-125" onerror="this.style.display='none'" src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=2200&q=85"/>
            <div class="absolute inset-0 hero-vignette"></div>
        </div>
        <div class="relative z-10 w-full max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 xl:col-span-8">
                    <div class="inline-flex items-center gap-3 glass-tile px-4 py-2 mb-6">
                        <span class="h-2 w-2 bg-brand-red shadow-[0_0_14px_#ff5540]"></span>
                        <span class="font-label-caps text-[11px] uppercase tracking-[0.28em] text-[#ffb4a8]">Member Training Portal</span>
                    </div>
                    <h1 class="font-display-xl text-[56px] md:text-[86px] lg:text-[100px] uppercase italic leading-[0.9] mb-6 drop-shadow-2xl">
                        PUSH HARDER,<br/><span class="text-brand-red">{{ $firstName }}</span>
                    </h1>
                    <p class="font-body-lg text-lg md:text-xl text-on-surface-variant max-w-2xl mb-8">
                        Akses QR check-in, pantau masa aktif membership, dan lanjutkan progres latihan Anda dari satu dashboard yang lebih cepat dan terpusat.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a class="btn-primary gap-3 w-full sm:w-auto" href="{{ route('barcode') }}">
                            <span class="material-symbols-outlined text-[22px]">qr_code_2</span>
                            Buka QR Check-In
                        </a>
                        <a href="{{ route('history') }}" class="inline-flex items-center justify-center gap-3 w-full sm:w-auto glass-tile px-6 py-4 font-label-caps text-xs uppercase tracking-[0.22em] text-white hover:text-brand-red">
                            <span class="material-symbols-outlined text-[18px]">monitoring</span>
                            Riwayat Latihan
                        </a>
                        @if($channelUrl)
                            <a href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-3 w-full sm:w-auto border border-brand-red/60 bg-brand-red/10 px-6 py-4 font-label-caps text-xs uppercase tracking-[0.22em] text-brand-red hover:bg-brand-red hover:text-black transition-colors">
                                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                WhatsApp
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl">
                        <div class="glass-tile p-4">
                            <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Check-In</span>
                            <span class="block font-display-xl text-4xl text-white mt-1">{{ $totalCheckins }}</span>
                        </div>
                        <div class="glass-tile p-4">
                            <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Sisa Aktif</span>
                            <span class="block font-display-xl text-4xl text-white mt-1">{{ $remainingDays ?? '-' }}<span class="text-lg text-brand-red">H</span></span>
                        </div>
                        <div class="glass-tile p-4">
                            <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Status</span>
                            <span class="block font-display-xl text-2xl text-brand-red uppercase mt-2">{{ str_replace('_', ' ', $membershipStatus ?? 'member') }}</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="glass-panel p-6 md:p-7 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-[3px] bg-brand-red"></div>
                        <div class="flex items-start justify-between gap-4 mb-8">
                            <div>
                                <p class="font-label-caps text-[10px] uppercase tracking-[0.24em] text-on-surface-variant">Membership Card</p>
                                <h2 class="font-headline-md text-3xl uppercase italic text-white mt-2">Arena Access</h2>
                            </div>
                            <span class="inline-flex h-12 w-12 items-center justify-center border border-brand-red/40 bg-brand-red/10 text-brand-red">
                                <span class="material-symbols-outlined">verified</span>
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                                <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Nama</span>
                                <span class="text-white font-semibold text-right">{{ $user->name }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                                <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Berlaku</span>
                                <span class="text-white font-semibold">{{ $expiresAt ? $expiresAt->format('d M Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="font-label-caps text-[10px] uppercase tracking-[0.18em] text-on-surface-variant">Mode</span>
                                <span class="text-brand-red font-label-caps text-xs uppercase tracking-[0.18em]">Ready To Train</span>
                            </div>
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-3">
                            <a href="{{ route('membership') }}" class="glass-tile p-4 flex items-center gap-3 text-white hover:text-brand-red">
                                <span class="material-symbols-outlined text-brand-red">workspace_premium</span>
                                <span class="font-label-caps text-[10px] uppercase tracking-[0.16em]">Membership</span>
                            </a>
                            <a href="{{ route('profile') }}" class="glass-tile p-4 flex items-center gap-3 text-white hover:text-brand-red">
                                <span class="material-symbols-outlined text-brand-red">person</span>
                                <span class="font-label-caps text-[10px] uppercase tracking-[0.16em]">Profil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative bg-[#0e0e0e] overflow-hidden py-20 md:py-28 border-y border-white/10">
        <div class="absolute inset-0 z-0">
            <img alt="High-tech Gym" class="w-full h-full object-cover opacity-20 grayscale contrast-125" onerror="this.style.display='none'" src="https://images.unsplash.com/photo-1540497077202-7c8a3999166f?auto=format&fit=crop&w=1920&q=80"/>
            <div class="absolute inset-0 bg-black/70 metal-grid"></div>
            <div class="scan-line"></div>
        </div>
        <div class="relative z-10 w-full max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <div class="lg:col-span-5 glass-panel p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <span class="font-label-caps text-brand-red tracking-[0.4em] uppercase block mb-4">Training Command</span>
                        <h2 class="font-display-xl text-5xl md:text-6xl uppercase italic leading-[0.92] mb-6 text-white">
                            SIAPKAN<br/><span class="text-brand-red">SESI HARI INI</span>
                        </h2>
                        <p class="font-body-lg text-on-surface-variant text-lg leading-relaxed">
                            Mulai dari QR check-in, pantau status aktif, lalu cek progres latihan tanpa harus berpindah alur terlalu jauh.
                        </p>
                    </div>
                    <div class="mt-8 flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3">
                        <a class="btn-primary gap-3 flex-1" href="{{ route('barcode') }}">
                            <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                            Check-In
                        </a>
                        <a class="glass-tile px-5 py-4 flex items-center justify-center gap-3 font-label-caps text-xs uppercase tracking-[0.2em] text-white hover:text-brand-red flex-1" href="{{ route('statistics') }}">
                            <span class="material-symbols-outlined text-[18px]">query_stats</span>
                            Statistik
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-tile p-6 min-h-[190px] flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <span class="inline-flex h-12 w-12 items-center justify-center bg-brand-red/10 border border-brand-red/35 text-brand-red">
                                <span class="material-symbols-outlined">monitoring</span>
                            </span>
                            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-[0.18em]">Performa</span>
                        </div>
                        <div>
                            <span class="block font-display-xl text-5xl text-white">{{ min(100, ($totalCheckins * 4) + 20) }}%</span>
                            <span class="font-label-caps text-[10px] text-brand-red uppercase tracking-[0.18em]">Kapasitas Aktivitas</span>
                        </div>
                    </div>
                    <div class="glass-tile p-6 min-h-[190px] flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <span class="inline-flex h-12 w-12 items-center justify-center bg-brand-red/10 border border-brand-red/35 text-brand-red">
                                <span class="material-symbols-outlined">timer</span>
                            </span>
                            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-[0.18em]">Aktivitas</span>
                        </div>
                        <div>
                            <span class="block font-display-xl text-5xl text-white">{{ number_format($totalCheckins * 1.5, 1) }}H</span>
                            <span class="font-label-caps text-[10px] text-brand-red uppercase tracking-[0.18em]">Estimasi Total Latihan</span>
                        </div>
                    </div>
                    <div class="glass-tile p-6 md:col-span-2 flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div>
                            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-[0.18em]">Tips Performa</span>
                            <h3 class="font-headline-md text-3xl uppercase italic text-white mt-2">Hidrasi sebelum latihan</h3>
                            <p class="text-on-surface-variant mt-2 max-w-2xl">Minum air sebelum latihan membantu fokus, tenaga, dan pemulihan setelah sesi.</p>
                        </div>
                        <div class="shrink-0 inline-flex h-16 w-16 items-center justify-center bg-brand-red text-black">
                            <span class="material-symbols-outlined text-[34px]">bolt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 md:py-28 bg-[#131313] metal-grid">
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <span class="font-label-caps text-brand-red tracking-[0.3em] uppercase block mb-4">Premium Gear</span>
                    <h2 class="font-headline-lg text-headline-lg uppercase italic mb-4">FASILITAS ELITE</h2>
                    <p class="font-body-lg text-on-surface-variant max-w-xl">
                        Peralatan kelas dunia dengan spesifikasi kompetisi, dirancang untuk keamanan maksimal dan hasil yang optimal.
                    </p>
                </div>
                <div class="h-[1px] flex-grow bg-surface-variant mx-8 hidden md:block"></div>
                <span class="font-label-caps text-brand-red uppercase tracking-widest border border-brand-red/40 bg-brand-red/10 px-4 py-2 shrink-0">Training Floor</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach([
                    ['Treadmill', 'Kardio Performa Tinggi', 'https://images.unsplash.com/photo-1576678927484-cc907957088c?auto=format&fit=crop&w=900&q=80'],
                    ['Smith Machine', 'Latihan Beban Terpadu', 'https://images.unsplash.com/photo-1534258936925-c58bed479fcb?auto=format&fit=crop&w=900&q=80'],
                    ['Pec Deck', 'Isolasi Otot Dada', 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=900&q=80'],
                    ['Leg Press', 'Power Majemuk Kaki', 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=900&q=80'],
                    ['Leg Extension', 'Definisi Quadriceps', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=80'],
                    ['Mesin Sit Up', 'Core & Abs Station', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=900&q=80'],
                    ['Twister Core', 'Stabilitas Rotasi', 'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?auto=format&fit=crop&w=900&q=80'],
                    ['Preacher Curl', 'Fokus Bicep Maksimal', 'https://images.unsplash.com/photo-1584863231364-2edc166de576?auto=format&fit=crop&w=900&q=80'],
                    ['Barbel & Dumbbell', 'Free Weights Elite', 'https://images.unsplash.com/photo-1586401100295-7a8096fd231a?auto=format&fit=crop&w=900&q=80'],
                    ['Lat Pulldown', 'Lebar Punggung Superior', 'https://images.unsplash.com/photo-1549060279-7e168fcee0c2?auto=format&fit=crop&w=900&q=80'],
                    ['Dip Machine', 'Triceps & Chest Power', 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?auto=format&fit=crop&w=900&q=80'],
                ] as [$title, $desc, $image])
                    <div class="group relative overflow-hidden aspect-square border border-white/10 bg-white/5 backdrop-blur-md shadow-[0_18px_60px_rgba(0,0,0,.28)]">
                        <img alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover grayscale opacity-50 group-hover:opacity-100 group-hover:scale-110 group-hover:grayscale-0 transition-all duration-700" onerror="this.style.display='none'" src="{{ $image }}"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-80"></div>
                        <div class="absolute top-4 left-4 inline-flex h-9 w-9 items-center justify-center bg-brand-red text-black opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="material-symbols-outlined text-[20px]">fitness_center</span>
                        </div>
                        <div class="absolute bottom-0 left-0 p-5 md:p-6 w-full translate-y-2 group-hover:translate-y-0 transition-transform">
                            <h3 class="font-headline-md text-xl uppercase text-white">{{ $title }}</h3>
                            <p class="text-[10px] font-label-caps text-brand-red opacity-0 group-hover:opacity-100 transition-opacity">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 md:py-28 bg-black border-t border-white/10">
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-4 glass-panel p-6 md:p-8">
                    <span class="font-label-caps text-brand-red tracking-[0.4em] uppercase block mb-4">Base Operations</span>
                    <h2 class="font-headline-lg text-headline-lg uppercase italic mb-8 leading-tight text-white">LOKASI KAMI</h2>
                    <div class="space-y-10">
                        <div class="flex gap-5">
                            <div class="bg-brand-red/10 p-3 h-fit border border-brand-red/30 text-brand-red">
                                <span class="material-symbols-outlined text-brand-red">location_on</span>
                            </div>
                            <div>
                                <p class="font-headline-md text-xl uppercase text-white mb-2">Arena Fitness</p>
                                <p class="text-on-surface-variant font-body-md leading-relaxed">
                                    Jl. Wilis Mukti III No.59, Campurejo,<br/>
                                    Kec. Mojoroto, Kabupaten Kediri,<br/>
                                    Jawa Timur 64116, Indonesia
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-5">
                            <div class="bg-brand-red/10 p-3 h-fit border border-brand-red/30 text-brand-red">
                                <span class="material-symbols-outlined text-brand-red">schedule</span>
                            </div>
                            <div>
                                <p class="font-headline-md text-xl uppercase text-white mb-2">Jam Operasional</p>
                                <p class="text-on-surface-variant font-body-md">Senin - Minggu: <span class="text-brand-red font-bold">24 JAM</span></p>
                                <p class="text-[10px] font-label-caps text-on-surface-variant/50 mt-1 uppercase italic">Selalu Siap Saat Anda Butuhkan</p>
                            </div>
                        </div>
                    </div>
                    <a class="mt-10 btn-primary w-full" href="https://www.google.com/maps/search/?api=1&query=-7.816346388525824,111.9867116109346" target="_blank" rel="noopener">
                        <span class="material-symbols-outlined text-[20px] mr-2">near_me</span>
                        Petunjuk Jalan
                    </a>
                </div>
                <div class="lg:col-span-8">
                    <div class="relative group">
                        <div class="relative border border-white/10 overflow-hidden bg-white/5 backdrop-blur-md shadow-[0_24px_80px_rgba(0,0,0,.42)]">
                            <iframe
                                class="w-full h-[360px] md:h-[520px] grayscale invert brightness-[0.7] contrast-[1.1] hover:grayscale-0 hover:invert-0 transition-all duration-700"
                                src="https://maps.google.com/maps?q=-7.816346388525824,111.9867116109346&z=17&output=embed"
                                title="Lokasi Arena Fitness"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <a class="absolute inset-0" href="https://www.google.com/maps/search/?api=1&query=-7.816346388525824,111.9867116109346" target="_blank" rel="noopener" aria-label="Buka detail lokasi di Google Maps"></a>
                            <div class="absolute top-6 left-6 glass-panel p-4 flex items-center gap-4">
                                <div class="w-3 h-3 bg-brand-red rounded-full animate-pulse shadow-[0_0_10px_#ff5540]"></div>
                                <span class="font-label-caps text-xs uppercase tracking-[0.2em] text-white">Arena Location</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 md:py-28 bg-[#0e0e0e] border-t border-white/10">
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="font-label-caps text-brand-red tracking-[0.3em] uppercase block mb-4">Feedback Portal</span>
                    <h2 class="font-headline-lg text-headline-lg uppercase italic mb-6 leading-tight">KRITIK &amp; SARAN</h2>
                    <p class="text-on-surface-variant font-body-lg max-w-md mb-8">
                        Visi kami adalah kesempurnaan. Bantu kami mencapainya dengan memberikan masukan Anda mengenai fasilitas dan layanan Arena Fitness.
                    </p>
                    <div class="flex gap-8">
                        <div class="flex flex-col">
                            <span class="font-display-xl text-3xl text-white">4.9/5</span>
                            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase mt-1">Kepuasan Member</span>
                        </div>
                        <div class="w-[1px] h-12 bg-surface-variant"></div>
                        <div class="flex flex-col">
                            <span class="font-display-xl text-3xl text-white">10k+</span>
                            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase mt-1">Komunitas Elit</span>
                        </div>
                    </div>
                </div>
                <div class="glass-panel p-6 md:p-10 relative">
                    <form method="POST" action="{{ route('member.feedback.submit') }}" class="space-y-6 relative z-10">
                        @csrf
                        <div>
                            <label class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-2 block tracking-widest">Kategori Masukan</label>
                            <select name="category" class="w-full bg-black/40 border border-white/10 p-4 text-on-surface focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all font-body-md outline-none" required>
                                <option value="Fasilitas Peralatan">Fasilitas Peralatan</option>
                                <option value="Layanan Personal Trainer">Layanan Personal Trainer</option>
                                <option value="Kebersihan Area">Kebersihan Area</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-2 block tracking-widest">Pesan Anda</label>
                            <textarea name="message" class="w-full bg-black/40 border border-white/10 p-4 text-on-surface focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all min-h-[150px] font-body-md outline-none" placeholder="Bagaimana kami bisa menjadi lebih baik untuk Anda?" required>{{ old('message') }}</textarea>
                        </div>
                        <button class="btn-primary w-full text-lg tracking-widest gap-3" type="submit">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                            KIRIM MASUKAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-black border-t border-surface-variant/30 w-full py-20">
    <div class="flex flex-col items-center justify-center gap-10 px-margin-mobile md:px-margin-desktop max-w-screen-2xl mx-auto text-center">
        <div class="font-display-xl text-brand-red text-4xl uppercase italic tracking-tighter">Arena Fitness</div>
        <div class="flex flex-wrap justify-center gap-x-12 gap-y-6">
            <a class="font-label-caps text-xs text-on-surface-variant hover:text-brand-red transition-all uppercase tracking-widest" href="#">Privacy Policy</a>
            <a class="font-label-caps text-xs text-on-surface-variant hover:text-brand-red transition-all uppercase tracking-widest" href="#">Terms of Service</a>
            <a class="font-label-caps text-xs text-on-surface-variant hover:text-brand-red transition-all uppercase tracking-widest" href="#">Support Center</a>
            <a class="font-label-caps text-xs text-on-surface-variant hover:text-brand-red transition-all uppercase tracking-widest" href="#">Careers</a>
        </div>
        <p class="font-label-caps text-[10px] text-on-surface-variant/40 tracking-[0.2em]">&copy; 2024 Arena Fitness. ALL RIGHTS RESERVED. DOMINASI SETIAP LIMIT.</p>
    </div>
</footer>
<script>
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.classList.add('bg-black/95', 'shadow-2xl');
            header.classList.remove('bg-black/55');
        } else {
            header.classList.remove('bg-black/95', 'shadow-2xl');
            header.classList.add('bg-black/55');
        }
    });
</script>
</body>
</html>

