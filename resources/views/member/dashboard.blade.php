<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>IRON ELITE | DOMINASI SETIAP LIMIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght=400;500;700&family=Hanken+Grotesk:wght=400;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-dim": "#131313",
                        "on-surface-variant": "#ebbbb4",
                        "on-surface": "#e2e2e2",
                        "background": "#131313",
                        "primary-fixed": "#ffdad4",
                        "on-primary-fixed-variant": "#930100",
                        "error-container": "#93000a",
                        "primary-fixed-dim": "#ffb4a8",
                        "primary": "#ffb4a8",
                        "surface-container": "#1f1f1f",
                        "tertiary-fixed-dim": "#c6c6c7",
                        "tertiary": "#c6c6c7",
                        "on-secondary-fixed-variant": "#474746",
                        "on-tertiary-fixed-variant": "#454747",
                        "secondary-fixed-dim": "#c8c6c5",
                        "on-primary": "#690100",
                        "surface-container-highest": "#353535",
                        "on-background": "#e2e2e2",
                        "tertiary-fixed": "#e2e2e2",
                        "secondary-fixed": "#e5e2e1",
                        "on-secondary-fixed": "#1c1b1b",
                        "on-primary-fixed": "#410000",
                        "on-tertiary-fixed": "#1a1c1c",
                        "on-error": "#690005",
                        "surface-container-high": "#2a2a2a",
                        "surface-variant": "#353535",
                        "surface-bright": "#393939",
                        "inverse-on-surface": "#303030",
                        "on-tertiary-container": "#282a2a",
                        "outline": "#b18780",
                        "surface-container-low": "#1b1b1b",
                        "error": "#ffb4ab",
                        "on-tertiary": "#2f3131",
                        "inverse-primary": "#c00100",
                        "tertiary-container": "#909191",
                        "surface-tint": "#ffb4a8",
                        "inverse-surface": "#e2e2e2",
                        "surface-container-lowest": "#0e0e0e",
                        "on-secondary": "#313030",
                        "surface": "#131313",
                        "secondary": "#c8c6c5",
                        "outline-variant": "#603e39",
                        "on-error-container": "#ffdad6",
                        "on-primary-container": "#5c0000",
                        "on-secondary-container": "#b7b5b4",
                        "secondary-container": "#474746",
                        "primary-container": "#ff5540",
                        "brand-red": "#FF0000"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        gutter: "32px",
                        unit: "8px",
                        margin-desktop: "64px",
                        section-padding: "120px",
                        margin-mobile: "20px"
                    },
                    fontFamily: {
                        "label-caps": ["JetBrains Mono"],
                        "body-md": ["Hanken Grotesk"],
                        "body-lg": ["Hanken Grotesk"],
                        "headline-lg-mobile": ["Oswald"],
                        "display-xl": ["Oswald"],
                        "headline-md": ["Oswald"],
                        "headline-lg": ["Oswald"]
                    },
                    fontSize: {
                        "label-caps": ["14px", {"lineHeight": "20px", "letterSpacing": "0.1em", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["36px", {"lineHeight": "40px", "fontWeight": "600"}],
                        "display-xl": ["80px", {"lineHeight": "90px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-md": ["32px", {"lineHeight": "38px", "fontWeight": "600"}],
                        "headline-lg": ["48px", {"lineHeight": "56px", "fontWeight": "600"}]
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'OPSZ' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }

        .metallic-gradient {
            background: linear-gradient(135deg, #222222 0%, #111111 100%);
        }

        .red-accent-bar {
            height: 4px;
            background-color: #FF0000;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .btn-primary {
            background-color: #FF0000;
            color: black;
            text-transform: uppercase;
            font-family: 'Oswald', sans-serif;
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-primary:hover {
            background-color: white;
            color: #FF0000;
            box-shadow: 4px 4px 0px white;
            transform: translate(-2px, -2px);
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            animation: marquee 30s linear infinite;
        }

        .glitch-hover:hover {
            animation: glitch 0.3s cubic-bezier(.25,.46,.45,.94) both infinite;
        }
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }

        @keyframes pulse-red {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .animate-pulse-red {
            animation: pulse-red 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-brand-red selection:text-white">
<!-- Top Navigation Bar -->
<header class="fixed top-0 w-full z-50 bg-background border-b border-surface-variant">
    <div class="flex justify-between items-center h-20 px-margin-mobile md:px-margin-desktop w-full max-w-screen-2xl mx-auto">
        <a class="inline-flex items-center gap-3" href="{{ route('member.dashboard') }}">
            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="width: 110px; height: auto;">
        </a>
        <nav class="hidden lg:flex items-center gap-8">
            <a class="font-label-caps text-label-caps text-primary border-b-2 border-primary pb-1 transition-colors duration-300" href="{{ route('member.dashboard') }}">Home</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('member.statistics') }}">Statistik</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="#">Barcode</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="#">Informasi Membership</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="#">Profil</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('member.history') }}">Riwayat</a>
        </nav>
        <div class="flex items-center gap-3 relative">
            <button id="notifBtn" type="button" class="w-10 h-10 rounded-full border border-surface-variant flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <div id="notifPopup" class="hidden absolute right-0 top-12 w-80 bg-surface-container border border-surface-variant rounded-lg p-3 z-50">
                <div class="text-sm font-bold mb-2">Pengumuman Admin</div>
                @forelse($announcements->take(5) as $announcement)
                    <div class="mb-2 pb-2 border-b border-surface-variant/40 last:border-0">
                        <div class="text-xs text-primary font-semibold">{{ $announcement->title }}</div>
                        <div class="text-xs text-on-surface-variant">{{ $announcement->body }}</div>
                    </div>
                @empty
                    <div class="text-xs text-on-surface-variant">Belum ada pengumuman.</div>
                @endforelse
            </div>
            <form method="POST" action="{{ route('member.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="bg-brand-red text-black font-label-caps px-6 py-2 uppercase tracking-widest active:scale-95 transition-transform">Logout</button>
            </form>
        </div>
    </div>
</header>
<!-- Gym Announcement Ticker -->
@if($announcements->count() > 0)
<div class="fixed top-20 w-full bg-brand-red text-black z-40 overflow-hidden py-1 border-y border-black">
    <div class="whitespace-nowrap flex animate-marquee font-label-caps text-xs uppercase font-bold">
        @foreach($announcements as $index => $announcement)
            @if($index > 0)
                <span class="mx-4 italic">•</span>
            @endif
            <span class="mx-4 italic">{{ $announcement->title }}: {{ $announcement->body }}</span>
        @endforeach
    </div>
</div>
@endif
<main class="pt-[112px]">
    <!-- Hero Section -->
    <section class="relative h-[850px] min-h-[600px] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover grayscale brightness-50" src="https://lh3.googleusercontent.com/aida/ADBb0uhiXhdMIfRnlL-HktO1jI6P-9rb78dysKtUZRDwlgBppANNsODy-3TrM8TQL2pXykabTxDv9URUCIG7ecCGflC6gUjXMppgjbUTdANJf1jtq1bHcfm6-YsMaJnNloBueUwNplfvy7JcibQ0CX59Cbykm63wjq9PRMlb6-AqgiQrJwa10ileUK3LYt9gLCW4aoj7_-Gr9laA5JhojMZmUhB2yKoaZRyHNc2pQFFaTDhXvTBPvU0K4SkDKOU" alt=""/>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>
        </div>
        <div class="relative z-10 w-full max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7">
                    <h1 class="font-display-xl text-display-xl uppercase italic leading-none mb-4 drop-shadow-2xl">
                        SELAMAT DATANG KEMBALI,<br/>
                        <span class="text-brand-red">{{ strtoupper(explode(' ', $user->name)[0]) }}!</span>
                    </h1>
                    <p class="font-label-caps text-primary uppercase tracking-widest mb-6 border-l-4 border-brand-red pl-6">
                        STATUS: {{ $member ? strtoupper($member->member_status) : 'MEMBER' }} | BERLAKU HINGGA: {{ $member && $member->expires_at ? $member->expires_at->format('d M Y') : 'N/A' }}
                    </p>
                    <!-- Level Progress Visual -->
                    <div class="grid grid-cols-1 gap-4 max-w-md">
                        <div class="bg-black/40 backdrop-blur-md p-6 border border-surface-variant/30 flex flex-col">
                            <span class="font-label-caps text-on-surface-variant text-[10px] uppercase mb-1">Total Kunjungan</span>
                            <span class="font-display-xl text-4xl">{{ $totalCheckins }}</span>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-5 space-y-6">
                    <!-- Daily Tip Card -->
                    <div class="bg-brand-red p-6 border border-white/20 relative group overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:rotate-12 transition-transform">
                            <span class="material-symbols-outlined text-8xl text-black">tips_and_updates</span>
                        </div>
                        <div class="relative z-10 text-black">
                            <span class="font-label-caps text-[10px] border-b border-black/30 pb-1 mb-3 inline-block font-bold">TIPS HARI INI</span>
                            <h4 class="font-headline-md text-lg uppercase mb-2">PENTINGNYA HIDRASI</h4>
                            <p class="font-body-md text-sm leading-tight italic font-semibold">"Minum setidaknya 500ml air 30 menit sebelum latihan untuk performa otot yang optimal."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ARENA PERFORMANCE DASHBOARD SECTION -->
    <section class="relative h-[700px] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img alt="Gym Interior Hero" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBjMMY9hC5ccpxsymIOq7eUHxIqmatGm0jJUFAix_ILHp-m1bUgHv7eThY-FrwrQ3qJkSCELN64_gYIv8waUEad460uSVGZBfKOq08AyzbBIQs3I9NG-0ta65eX2lBKQux_m6Z8M4no_Tg4mhYB1p3jtBdTzLWShK7kduLnPNqUblVOVW6Mx6bFVcFZLA3Z9MDm_Ez8VmSgmZml3vsAoWUToRCdp4GS4KbJke-tRGNUge-_iMVAuK4XCLHOZUltLimxCzQTCVFwppX" alt=""/>
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/40 to-transparent"></div>
        </div>
        <div class="relative z-10 w-full max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="max-w-3xl">
                <span class="font-label-caps text-brand-red tracking-[0.3em] uppercase block mb-4">Arena Elit</span>
                <h2 class="font-display-xl text-6xl md:text-7xl uppercase italic leading-none mb-6 text-white">
                    DIBANGUN UNTUK <br/>
                    <span class="text-brand-red">KEUNGGULAN</span>
                </h2>
                <p class="font-body-lg text-on-surface-variant max-w-xl mb-8">
                    Rasakan pengalaman latihan di fasilitas industri modern dengan peralatan standar kompetisi dunia. Setiap sudut dirancang untuk memacu limit Anda.
                </p>
                <button class="bg-brand-red text-black font-label-caps px-10 py-4 uppercase tracking-widest hover:bg-white transition-colors duration-300">
                    Lihat Fasilitas
                </button>
            </div>
        </div>
    </section>
    <!-- Membership Info Snippet -->
    <section class="py-section-padding bg-black relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-brand-red/5 skew-x-[-20deg] translate-x-24"></div>
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop relative z-10">
            <div class="grid grid-cols-1 gap-16 items-center max-w-2xl mx-auto">
                <div>
                    <span class="font-label-caps text-brand-red text-label-caps uppercase tracking-[0.3em] mb-4 block">Your Progress</span>
                    <h2 class="font-headline-lg text-headline-lg uppercase mb-8 leading-tight">STATUS MEMBERSHIP<br/>ANDA</h2>
                    <div class="space-y-8">
                        <div>
                            <div class="flex justify-between font-label-caps mb-2 text-sm uppercase">
                                <span>Menuju Tier Berikutnya</span>
                                <span class="text-brand-red">85%</span>
                            </div>
                            <div class="w-full h-2 bg-surface-container-high overflow-hidden">
                                <div class="h-full bg-brand-red" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="bg-surface-container-high p-3 border border-brand-red/30">
                                <span class="material-symbols-outlined text-brand-red">military_tech</span>
                            </div>
                            <div>
                                <h4 class="font-headline-md text-xl uppercase mb-1">Keuntungan Member Anda</h4>
                                <p class="text-on-surface-variant font-body-md">Akses penuh ke semua fasilitas beban, sauna, dan locker room premium.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- Footer -->
<section class="py-section-padding bg-surface-container-lowest border-t border-surface-variant/30">
    <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div>
                <span class="font-label-caps text-brand-red text-label-caps uppercase tracking-[0.3em] mb-4 block">Your Feedback</span>
                <h2 class="font-headline-lg text-headline-lg uppercase mb-4 leading-tight">KRITIK &amp; SARAN</h2>
                <p class="text-on-surface-variant font-body-lg max-w-md">Bantu kami menjadi lebih baik. Sampaikan masukan atau saran Anda mengenai fasilitas dan layanan Iron Elite Gym.</p>
            </div>
            <div class="bg-surface-container p-8 border border-surface-variant">
                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-900/40 border border-green-500 text-green-100 rounded">{{ session('status') }}</div>
                @endif
                <form class="space-y-6" method="POST" action="{{ route('member.feedback.store') }}">
                    @csrf
                    <div>
                        <label class="font-label-caps text-xs text-on-surface-variant uppercase mb-2 block">Subjek</label>
                        <input name="subject" class="w-full bg-black/40 border border-surface-variant p-4 text-on-surface focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all font-body-md" placeholder="Contoh: Saran fasilitas" required>
                    </div>
                    <div>
                        <label class="font-label-caps text-xs text-on-surface-variant uppercase mb-2 block">Pesan Anda</label>
                        <textarea name="message" class="w-full bg-black/40 border border-surface-variant p-4 text-on-surface focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all min-h-[150px] font-body-md" placeholder="Tulis masukan Anda di sini..." required></textarea>
                    </div>
                    <button class="w-full py-4 bg-brand-red text-black hover:bg-white transition-all uppercase font-headline-md tracking-wider text-lg" type="submit">
                        KIRIM
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
<section class="py-section-padding bg-background">
    <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="mb-16">
            <h2 class="font-headline-lg text-headline-lg uppercase mb-2">FASILITAS ELIT</h2>
            <div class="w-24 h-1 bg-brand-red mb-4"></div>
            <p class="font-body-lg text-body-lg text-on-surface-variant">Peralatan kelas dunia untuk performa tanpa batas.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Treadmill -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Treadmill" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida/ADBb0ujDW0G6B84XN476Wj5oeTol7ZRhCMRZQFUTf9Jz8kuDKHKIyno660TXzg3WiQPGDuBIi43EU0MySQH50OVOWjj53C4KfqeRLankicLdIMUDPf18rfzmwqyo-OzwaiAgzoXdchKRszgMara5PZ7_nIFszcEzmdste0J0dWc_nPiq3DMJqZr6HAebTqnknVpVu5D1XUUyErYELjdEk6QOOwxUObJdOKnazLvXfR5L_fX-YqbGrvMUhrYlKLt" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Treadmill</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
            <!-- Smith Machine -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Smith Machine" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0s96lgIYpjlghzJOorhhQAizkLaTjsotpKPBWIfqqce6Vgp81yWQZCAE9ly8_U8Ux5AMuT_YKUStBgv-68aTo3DyNvp2PM-ZMGr1_9nBbF4H-C5kFEVafxdVgCva9guuBrj-Yr53V7h49R5Ko0PfSxXpiIKO4uAfhY8zBDN_Ab84SLwlcSF_9SqJEGTNM2c7tNsd1gP0yKoKx4rpjPGEi-0UEzlVQGZQQtbvXOcVNQjbLIEry8pN1sdEYIwbj-QZ4WI9QtuERYH0-j" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Smith Machine</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
            <!-- Pec Deck -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Pec Deck" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC6331HSGLaijkAjEkNi7vSiv6WV6S6CQ8dY-DCw78Pwa9FuXCNpKE4Tac3ANl9lOJ_JlTpWNbTsid0oG7smFpygam6hu2DmUV_p4C64Qdx18GjDdtOOKsww9CG_8TG1exBtIGFwqE6Xf_xjsDPOuCvlmTP-8nXcZJu8_3QhgK4bhbkdI4mshzo3MbZIat7dZSmmvwYQV_JnFxR4Y1bdh1kzeCNlA3HE9qX4MHc7dZGP7YKQzljVvQjUnRiC0PcKcRFGss83R-PUhLC" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Pec Deck</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
            <!-- Leg Press -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Leg Press" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCNpvVUC18EV6F1dBVNNBecGzpy3VfkenEY65uSXOxKNr9lLHJbOjrwH-k5DVm0M7rKZqH7l6qvaDAQ5kG4iorrnMobmAj6SGSdJu5Awt7c2cYwVr1MXn5tTB7-TSkRpDnYh2EcWOKzewazstvHF72BY6y1ywX1v3TCgD8bSjQrRTkrwxX5pxSjz7-xZbBgUxzLfozGvjcvUD4wL8x4PtokT-OwYa8J8ALiMDodsIZLXQCAeKnx10mV_2jvbJczsRHbBTauYVhM5RSc" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Leg Press</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
            <!-- Leg Extension -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Leg Extension" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCvvopvDjKkZEKf_qd02TSxMzypneQ0e5-AZtZlFZXtaj2LzJBWuiK3-ruwvjYd11I7GFtIzAwQTAIRMvzgB74yEK80eD1V7ecikhHxusZ3oBN4TgE0fi3BPE3UnIV9-EURY438yueaDopmMzs5L6UfuWsFqJSnVMnjMYhYG_DC8whDl75NgmRJNnte19WjyZIRZ3u_JwYD2dmhmhx6TdQdbbQ8BeoXNFB8wC_V7cyQGLo-uklSoe94dkwtDZSPT6siOrCzqABvfIxt" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Leg Extension</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
            <!-- Sit Up Machine -->
            <div class="group relative overflow-hidden bg-surface-container aspect-square border border-surface-variant">
                <img alt="Mesin Sit Up" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLdp2LEHJ7ognSjgM-jVT7xyrM2waUqumXsVxw2zyVGCUbVvJThNqfuN23_DRl4I_jwYEFUyCLnfW37MOoJz0zUATSGV5Q6lvKsnBe1YcxwW_cvce3T-zVuZKxq7qh8qNB17vlbWXOla5zvK9ehoClhC6B9UsdW3vme3XL8OvPuYcXci4kUBsj0JR4YOeZEJEQ3Y0hfzMfobkhENjtXZ2_kGZ7NYm7Swx7MtNBGgA3s8bcstU912EKyTSuMkqCGP3TFKbwiNsEnr8t" alt=""/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4 w-full">
                    <h3 class="font-headline-md text-base uppercase">Mesin Sit Up</h3>
                    <div class="h-1 w-0 group-hover:w-full bg-brand-red transition-all duration-300 mt-1"></div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script>
    (() => {
        const btn = document.getElementById('notifBtn');
        const popup = document.getElementById('notifPopup');
        if (!btn || !popup) return;
        btn.addEventListener('click', () => popup.classList.toggle('hidden'));
        document.addEventListener('click', (e) => {
            if (!popup.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
                popup.classList.add('hidden');
            }
        });
    })();
</script>
</html>
