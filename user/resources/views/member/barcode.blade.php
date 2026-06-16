<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>QR Code Presensi | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Hanken+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#131313',
                        surface: '#131313',
                        'surface-container': '#1f1f1f',
                        'surface-container-high': '#2a2a2a',
                        'surface-variant': '#353535',
                        'on-background': '#e2e2e2',
                        'on-surface': '#e2e2e2',
                        'on-surface-variant': '#ebbbb4',
                        primary: '#ffb4a8',
                        'primary-container': '#ff5540',
                        'on-primary-fixed': '#410000',
                    },
                    spacing: {
                        'margin-desktop': '64px',
                        'margin-mobile': '20px',
                        'section-padding': '120px',
                        gutter: '32px',
                    },
                    fontFamily: {
                        'body-md': ['Hanken Grotesk'],
                        'body-lg': ['Hanken Grotesk'],
                        'label-caps': ['JetBrains Mono'],
                        'headline-md': ['Oswald'],
                        'headline-lg': ['Oswald'],
                        'headline-lg-mobile': ['Oswald'],
                        'display-xl': ['Oswald'],
                    },
                    fontSize: {
                        'body-md': ['16px', {lineHeight: '24px', fontWeight: '400'}],
                        'body-lg': ['18px', {lineHeight: '28px', fontWeight: '400'}],
                        'label-caps': ['14px', {lineHeight: '20px', letterSpacing: '0.1em', fontWeight: '500'}],
                        'headline-md': ['32px', {lineHeight: '38px', fontWeight: '600'}],
                        'headline-lg-mobile': ['36px', {lineHeight: '40px', fontWeight: '600'}],
                        'headline-lg': ['48px', {lineHeight: '56px', fontWeight: '600'}],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .scan-line {
            height: 2px;
            background: #ff5540;
            box-shadow: 0 0 15px #ff5540;
            position: absolute;
            width: 100%;
            top: 0;
            animation: scan 3s linear infinite;
        }
        @keyframes scan {
            0% { top: 0%; opacity: 0; }
            5% { opacity: 1; }
            95% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .bg-industrial-grain {
            background-color: #131313;
            background-image: url("https://www.transparenttextures.com/patterns/dark-matter.png");
        }
        .neon-border {
            box-shadow: 0 0 10px rgba(255, 85, 64, 0.2), inset 0 0 10px rgba(255, 85, 64, 0.1);
        }
        .bg-background { background-color: #131313; }
        .bg-surface { background-color: #131313; }
        .bg-surface-container-high { background-color: #2a2a2a; }
        .border-surface-variant { border-color: #353535; }
        .text-on-background, .text-on-surface { color: #e2e2e2; }
        .text-on-surface-variant { color: #ebbbb4; }
        .text-primary { color: #ffb4a8; }
        .bg-primary-container { background-color: #ff5540; }
        .text-on-primary-fixed { color: #410000; }
        .font-body-md, .font-body-lg { font-family: 'Hanken Grotesk', sans-serif; }
        .font-label-caps { font-family: 'JetBrains Mono', monospace; }
        .font-display-xl, .font-headline-lg, .font-headline-md { font-family: 'Oswald', sans-serif; }
        .text-label-caps { font-size: 14px; line-height: 20px; letter-spacing: 0.1em; font-weight: 500; }
        .text-body-md { font-size: 16px; line-height: 24px; font-weight: 400; }
        .text-body-lg { font-size: 18px; line-height: 28px; font-weight: 400; }
        .text-headline-md { font-size: 32px; line-height: 38px; font-weight: 600; }
        .text-headline-lg { font-size: 48px; line-height: 56px; font-weight: 600; }
        .text-headline-lg-mobile { font-size: 36px; line-height: 40px; font-weight: 600; }
        .px-margin-mobile { padding-left: 20px; padding-right: 20px; }
        .py-section-padding { padding-top: 120px; padding-bottom: 120px; }
        .gap-gutter { gap: 32px; }
        .mb-gutter { margin-bottom: 32px; }
        .mt-gutter { margin-top: 32px; }
        @media (min-width: 768px) {
            .md\:px-margin-desktop { padding-left: 64px; padding-right: 64px; }
        }
    </style>
    <link href="{{ asset('css/member-responsive.css') }}?v={{ file_exists(public_path('css/member-responsive.css')) ? filemtime(public_path('css/member-responsive.css')) : time() }}" rel="stylesheet"/>
</head>
<body class="bg-background text-on-background font-body-md overflow-x-hidden">
@include('member.partials.page-loader')
<header class="fixed top-0 w-full z-50 bg-background border-b border-surface-variant">
    <div class="grid grid-cols-[1fr_auto_1fr] items-center h-20 px-margin-mobile md:px-margin-desktop w-full max-w-screen-2xl mx-auto">
        <a class="justify-self-start inline-flex items-center gap-3" href="{{ route('dashboard') }}" aria-label="Arena Fitness Home">
            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="display:block;width:3.8rem;max-width:3.8rem;height:3rem;max-height:3rem;object-fit:contain;border-radius:1rem;box-shadow:0 18px 32px rgba(255,59,59,.18);background:rgba(255,255,255,.04);flex:0 0 auto;">
            <span class="font-display-xl text-white uppercase italic text-2xl tracking-tighter leading-none hidden sm:inline">Arena <span class="text-brand-red">Fitness</span></span>
        </a>
        <nav class="hidden lg:flex items-center justify-center gap-10 justify-self-center">
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('dashboard') }}">Home</a>
            <a class="font-label-caps text-label-caps text-primary border-b-2 border-primary pb-1 transition-colors duration-300" href="{{ route('barcode') }}">QR Code</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('membership') }}">Membership</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('profile') }}">Profil</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="{{ route('history') }}">Riwayat</a>
        </nav>
        <div class="flex items-center gap-3 justify-self-end">
        @include('member.partials.notifications')
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="font-label-caps text-label-caps bg-[#ff5540] text-black px-5 py-2 uppercase tracking-widest hover:bg-white hover:text-[#ff5540] transition-colors duration-300">LOGOUT</button>
        </form>
        </div>
    </div>
</header>

<main class="min-h-screen pt-20 flex flex-col items-center justify-center bg-industrial-grain relative">
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="absolute -top-1/4 -right-1/4 w-[600px] h-[600px] bg-primary/5 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-[600px] h-[600px] bg-primary/5 blur-[120px] rounded-full"></div>
    </div>

    <div class="container mx-auto px-margin-mobile flex flex-col items-center text-center z-10 py-section-padding">
        <div class="mb-gutter max-w-2xl">
            <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase tracking-tight mb-4">
                QR CODE <span class="text-primary italic">PRESENSI</span>
            </h1>
            <p class="text-on-surface-variant font-body-lg text-body-md md:text-body-lg opacity-80">
                Pindai QR code Anda pada reader di pintu masuk untuk memulai latihan.
            </p>
        </div>

        <div class="relative group">
            <div class="absolute -top-2 -left-2 w-8 h-8 border-t-2 border-l-2 border-primary"></div>
            <div class="absolute -top-2 -right-2 w-8 h-8 border-t-2 border-l-2 rotate-90 border-primary"></div>
            <div class="absolute -bottom-2 -left-2 w-8 h-8 border-t-2 border-l-2 -rotate-90 border-primary"></div>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 border-t-2 border-l-2 rotate-180 border-primary"></div>

            <div class="bg-surface-container-high p-6 md:p-8 rounded-none border border-surface-variant neon-border relative overflow-hidden">
                <div class="scan-line"></div>
                <div class="w-64 h-64 md:w-80 md:h-80 bg-white flex items-center justify-center relative shadow-2xl p-5">
                    @if($checkinCode)
                        <div id="member-qr" class="w-full h-full flex items-center justify-center"></div>
                    @else
                        <div class="text-center text-black">
                            <div class="font-headline-md text-2xl uppercase">Kode Belum Tersedia</div>
                            <div class="font-label-caps text-xs mt-3">Hubungi admin untuk membuat kode check-in.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-gutter flex flex-col items-center gap-2">
            <div class="font-display-xl text-headline-md uppercase tracking-wide text-on-surface">
                {{ $member?->full_name ?? $user?->name ?? 'Member' }}
            </div>
            <div class="flex items-center gap-2 bg-primary/10 border border-primary/30 px-4 py-1.5">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                <span class="font-label-caps text-label-caps text-primary">STATUS: {{ strtoupper(str_replace('_', ' ', $membershipStatus ?? 'member')) }}</span>
            </div>
            @if($checkinCode)
                <div class="font-label-caps text-label-caps text-on-surface-variant/70 mt-2">
                    KODE: {{ $checkinCode }}
                </div>
            @endif
        </div>

        <div class="mt-12">
            <a class="inline-flex items-center gap-3 border-2 border-on-surface text-on-surface hover:bg-on-surface hover:text-black transition-all duration-300 px-8 py-4 font-label-caps text-label-caps group" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                KEMBALI KE DASHBOARD
            </a>
        </div>

        <div class="mt-12 font-label-caps text-label-caps text-on-surface-variant/40 flex flex-wrap justify-center items-center gap-4">
            <span id="live-clock">00:00:00</span>
            <span class="w-1 h-1 bg-on-surface-variant/40 rounded-full"></span>
            <span>SESSION_ID: {{ $sessionId }}</span>
        </div>
    </div>
</main>

<footer class="bg-surface border-t border-surface-variant flex flex-col md:flex-row justify-between items-center px-margin-mobile md:px-margin-desktop py-12 gap-gutter w-full">
    <div class="font-headline-md text-headline-md text-on-surface">
        Arena Fitness
    </div>
    <div class="flex flex-wrap justify-center gap-gutter">
        <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-on-surface transition-colors" href="#">PRIVACY</a>
        <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-on-surface transition-colors" href="#">TERMS</a>
        <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-on-surface transition-colors" href="#">MEMBERSHIP</a>
        <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-on-surface transition-colors" href="#">SUPPORT</a>
    </div>
    <div class="font-label-caps text-label-caps text-on-surface-variant/60">
        &copy; 2024 Arena Fitness. NO EXCUSES.
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const checkinCode = @json($checkinCode);

    function updateClock() {
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0') + ':' +
            now.getSeconds().toString().padStart(2, '0');
        document.getElementById('live-clock').textContent = timeString;
    }

    if (checkinCode && window.QRCode) {
        new QRCode(document.getElementById('member-qr'), {
            text: checkinCode,
            width: 280,
            height: 280,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>
