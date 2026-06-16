<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Pemberitahuan | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#131313',
                        'surface-container': '#1f1f1f',
                        'surface-container-lowest': '#0e0e0e',
                        'surface-variant': '#353535',
                        'on-background': '#e2e2e2',
                        'on-surface': '#e2e2e2',
                        'on-surface-variant': '#ebbbb4',
                        primary: '#ffb4a8',
                        'primary-container': '#ff5540',
                        'brand-red': '#FF0000',
                    },
                    spacing: {
                        gutter: '32px',
                        'margin-mobile': '20px',
                        'margin-desktop': '64px',
                        'section-padding': '120px',
                    },
                    fontFamily: {
                        'label-caps': ['JetBrains Mono'],
                        'body-md': ['Hanken Grotesk'],
                        'body-lg': ['Hanken Grotesk'],
                        'headline-md': ['Oswald'],
                        'headline-lg': ['Oswald'],
                        'display-xl': ['Oswald'],
                    },
                    fontSize: {
                        'label-caps': ['14px', {lineHeight: '20px', letterSpacing: '0.1em', fontWeight: '500'}],
                        'body-md': ['16px', {lineHeight: '24px', fontWeight: '400'}],
                        'body-lg': ['18px', {lineHeight: '28px', fontWeight: '400'}],
                        'headline-md': ['32px', {lineHeight: '38px', fontWeight: '600'}],
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
        .bg-background { background-color: #131313; }
        .bg-surface-container { background-color: #1f1f1f; }
        .bg-surface-container-lowest { background-color: #0e0e0e; }
        .border-surface-variant { border-color: #353535; }
        .text-primary { color: #ffb4a8; }
        .text-brand-red { color: #FF0000; }
        .text-on-background, .text-on-surface { color: #e2e2e2; }
        .text-on-surface-variant { color: #ebbbb4; }
        .font-body-md, .font-body-lg { font-family: 'Hanken Grotesk', sans-serif; }
        .font-label-caps { font-family: 'JetBrains Mono', monospace; }
        .font-display-xl, .font-headline-lg, .font-headline-md { font-family: 'Oswald', sans-serif; }
        .text-label-caps { font-size: 14px; line-height: 20px; letter-spacing: 0.1em; font-weight: 500; }
        .text-body-md { font-size: 16px; line-height: 24px; font-weight: 400; }
        .text-body-lg { font-size: 18px; line-height: 28px; font-weight: 400; }
        .text-headline-md { font-size: 32px; line-height: 38px; font-weight: 600; }
        .text-headline-lg { font-size: 48px; line-height: 56px; font-weight: 600; }
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
@endphp
<main>
    <section class="relative py-8 border-b border-surface-variant overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="font-label-caps text-brand-red uppercase tracking-[0.3em] block mb-3">Pemberitahuan Member</span>
                    <h1 class="font-display-xl text-headline-lg uppercase italic leading-none mb-0">Warning Admin</h1>
                </div>
                <div class="flex items-center gap-3">
                    @include('member.partials.notifications')
                    <a href="{{ route('dashboard') }}" class="font-label-caps text-xs uppercase tracking-[0.2em] border border-brand-red px-4 py-2 text-brand-red hover:bg-brand-red hover:text-black transition-colors">Home</a>
                </div>
            </div>
            @if($channelUrl)
                <a href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 mt-4 border border-brand-red bg-brand-red/10 px-4 py-2 font-label-caps text-xs uppercase tracking-[0.25em] text-brand-red hover:bg-brand-red hover:text-black transition-colors">
                    Gabung Saluran WhatsApp
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                </a>
            @endif
        </div>
    </section>

    <section class="py-8 bg-surface-container-lowest">
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop">
            <div class="grid grid-cols-1 gap-4 max-w-3xl">
                @forelse($announcements as $announcement)
                    <article id="notice-{{ $announcement->id }}" class="bg-surface-container border border-surface-variant p-4 md:p-5 scroll-mt-8 target:border-brand-red target:bg-brand-red/10 transition-colors">
                        <div class="flex items-start gap-4">
                            <div class="bg-primary/10 border border-primary/30 p-3 shrink-0">
                                <span class="material-symbols-outlined text-primary">campaign</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <h2 class="font-headline-md text-2xl uppercase text-on-surface">{{ $announcement->title }}</h2>
                                    <span class="font-label-caps text-[10px] uppercase text-brand-red border border-brand-red/40 px-2 py-1">
                                        {{ $announcement->publish_at ? $announcement->publish_at->format('d M Y H:i') : $announcement->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>
                                <p class="font-body-md text-on-surface-variant whitespace-pre-line">{{ preg_replace('/^\[TARGET_MEMBER_ID:\d+\]\s*/', '', $announcement->body) }}</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-surface-container border border-surface-variant p-10 text-center">
                        <span class="material-symbols-outlined text-primary text-5xl mb-4">notifications_off</span>
                        <h2 class="font-headline-md text-2xl uppercase mb-2">Belum Ada Warning</h2>
                        <p class="font-body-md text-on-surface-variant">Belum ada pesan warning membership yang dikirim admin ke akun Anda.</p>
                        @if($channelUrl)
                            <a href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 mt-6 border border-brand-red bg-brand-red/10 px-6 py-3 font-label-caps text-xs uppercase tracking-[0.25em] text-brand-red hover:bg-brand-red hover:text-black transition-colors">
                                Buka Saluran WhatsApp
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>

</body>
</html>
