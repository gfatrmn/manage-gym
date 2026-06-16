<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Profil | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: { 'brand-red': '#ff5540' }, fontFamily: { display: ['Oswald'], mono: ['JetBrains Mono'], body: ['Hanken Grotesk'] } } }
        };
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
        .field { width: 100%; background: rgba(0,0,0,.62); border: 1px solid rgba(255,255,255,.18); color: #fff; padding: 1rem; outline: none; transition: border-color .2s ease, background .2s ease; }
        .field:focus { border-color: #ff5540; background: rgba(0,0,0,.52); }
        .info-card { background: linear-gradient(135deg, rgba(255,85,64,.12), rgba(255,255,255,.055)); border: 1px solid rgba(255,255,255,.14); }
        .btn-primary { background: #ff5540; color: #080808; display: inline-flex; align-items: center; justify-content: center; gap: .65rem; padding: 1rem 1.35rem; font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; transition: all .25s ease; }
        .btn-primary:hover { background: #fff; color: #ff5540; transform: translateY(-2px); }
    </style>
    <link href="{{ asset('css/member-responsive.css') }}?v={{ file_exists(public_path('css/member-responsive.css')) ? filemtime(public_path('css/member-responsive.css')) : time() }}" rel="stylesheet"/>
</head>
<body class="bg-[#131313] text-[#e2e2e2] font-body selection:bg-brand-red selection:text-black">
@include('member.partials.page-loader')
@php
    $photoLimitReached = (int) ($photoChangesRemaining ?? 0) <= 0;
    $memberName = $member?->full_name ?? $user?->name ?? 'Member';
    $initial = mb_substr($memberName, 0, 1);
    $statusLabel = strtoupper(str_replace('_', ' ', $membershipStatus ?? 'member'));
@endphp

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
            <a class="nav-link active text-brand-red" href="{{ route('profile') }}">Profil</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('history') }}">Riwayat</a>
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
            <img alt="Arena Fitness profile" class="h-full w-full object-cover grayscale brightness-[0.35] contrast-125" src="https://images.unsplash.com/photo-1540497077202-7c8a3999166f?auto=format&fit=crop&w=2200&q=85" onerror="this.style.display='none'"/>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/85 to-black/35"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#131313] via-transparent to-transparent"></div>
        </div>
        <div class="relative max-w-screen-2xl mx-auto px-5 md:px-16 py-10 md:py-16">
            @if(session('status'))
                <div class="mb-5 border border-green-500/30 bg-green-500/10 px-5 py-4 text-green-300">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-5 border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-300">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <aside class="lg:col-span-4 glass-panel p-6 md:p-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative h-52 w-52 border border-brand-red/45 bg-black/50 overflow-hidden shadow-[0_0_40px_rgba(255,85,64,.18)]">
                            @if($member?->profile_photo_url)
                                <img class="h-full w-full object-cover grayscale contrast-125" alt="{{ $memberName }}" src="{{ $member->profile_photo_url }}"/>
                            @else
                                <div class="h-full w-full flex items-center justify-center text-brand-red font-display text-8xl uppercase">{{ $initial }}</div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 bg-black/70 px-4 py-3 backdrop-blur-md">
                                <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-red">Arena Member</p>
                            </div>
                        </div>
                        <h1 class="font-display text-4xl md:text-5xl uppercase italic text-white mt-6 leading-none">{{ $memberName }}</h1>
                        <p class="font-mono text-xs uppercase tracking-[0.22em] text-brand-red mt-3">{{ $statusLabel }}</p>

                        <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-6 w-full">
                            @csrf
                            @if($pendingPhotoRequest)
                                <button type="button" class="w-full border border-yellow-500/30 bg-yellow-500/10 px-5 py-4 font-mono text-xs uppercase tracking-[0.18em] text-yellow-300 cursor-not-allowed" disabled>
                                    <span class="material-symbols-outlined text-[18px] align-middle mr-2">hourglass_empty</span>Menunggu Persetujuan
                                </button>
                            @elseif(!$photoLimitReached)
                                <input id="quick-profile-photo" class="sr-only" name="profile_photo" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="this.form.submit()"/>
                                <label for="quick-profile-photo" class="btn-primary w-full cursor-pointer"><span class="material-symbols-outlined text-[18px]">photo_camera</span>Ganti Foto</label>
                            @else
                                <input id="quick-profile-photo" class="sr-only" name="profile_photo" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" onchange="this.form.submit()"/>
                                <label for="quick-profile-photo" class="btn-primary w-full cursor-pointer bg-amber-600 hover:bg-amber-700 border-amber-600">
                                    <span class="material-symbols-outlined text-[18px]">file_upload</span>Ajukan Ganti Foto
                                </label>
                            @endif
                        </form>

                        <div class="mt-5 w-full glass-tile p-4 text-left">
                            <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Jatah Ganti Foto</p>
                            <p class="text-white mt-2">Sisa ganti langsung: <span class="text-brand-red font-semibold">{{ $photoChangesRemaining }}x</span></p>
                            @if($pendingPhotoRequest)
                                <p class="text-yellow-300 text-sm mt-2">Foto baru menunggu persetujuan admin.</p>
                            @endif
                        </div>
                        <div class="mt-4 w-full grid grid-cols-2 gap-3">
                            <a href="{{ route('barcode') }}" class="glass-tile p-4 text-left text-white hover:text-brand-red">
                                <span class="material-symbols-outlined text-brand-red mb-3">qr_code_scanner</span>
                                <p class="font-mono text-[10px] uppercase tracking-[0.16em]">Check-In Gym</p>
                            </a>
                            <a href="{{ route('history') }}" class="glass-tile p-4 text-left text-white hover:text-brand-red">
                                <span class="material-symbols-outlined text-brand-red mb-3">fitness_center</span>
                                <p class="font-mono text-[10px] uppercase tracking-[0.16em]">Log Latihan</p>
                            </a>
                        </div>
                    </div>
                </aside>

                <section class="lg:col-span-8 flex flex-col gap-4">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="glass-tile p-5"><p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Check-In</p><p class="font-display text-4xl text-white mt-2">{{ $totalCheckins }}</p></div>
                        <div class="glass-tile p-5"><p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Plan</p><p class="font-display text-2xl text-white mt-3">{{ $member?->membership_plan ?: '-' }}</p></div>
                        <div class="glass-tile p-5"><p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Joined</p><p class="font-display text-2xl text-white mt-3">{{ $member?->joined_at ? $member->joined_at->format('M Y') : '-' }}</p></div>
                        <div class="glass-tile p-5"><p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Expired</p><p class="font-display text-2xl text-brand-red mt-3">{{ $member?->expires_at ? $member->expires_at->format('d M') : '-' }}</p></div>
                    </div>

                    <div class="glass-panel p-6 md:p-8">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <div>
                                <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">Data Akun Member</span>
                                <h2 class="font-display text-4xl uppercase italic text-white mt-2">Identitas & Kontak</h2>
                            </div>
                            <a href="{{ route('membership') }}" class="glass-tile inline-flex items-center justify-center gap-3 px-5 py-3 font-mono text-xs uppercase tracking-[0.16em] text-white hover:text-brand-red"><span class="material-symbols-outlined text-[18px]">workspace_premium</span>Membership</a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            <div class="info-card p-5 flex items-start gap-4">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center bg-brand-red text-black"><span class="material-symbols-outlined text-[22px]">mail</span></span>
                                <div class="min-w-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Email</p>
                                    <p class="mt-2 text-white font-semibold break-words">{{ $user?->email ?? $member?->email ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="info-card p-5 flex items-start gap-4">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center bg-brand-red text-black"><span class="material-symbols-outlined text-[22px]">call</span></span>
                                <div class="min-w-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Nomor HP</p>
                                    <p class="mt-2 text-white font-semibold break-words">{{ $member?->phone ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="info-card p-5 flex items-start gap-4">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center bg-brand-red text-black"><span class="material-symbols-outlined text-[22px]">account_circle</span></span>
                                <div class="min-w-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Username Login</p>
                                    <p class="mt-2 text-white font-semibold break-words">{{ $user?->login ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="info-card p-5 flex items-start gap-4">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center bg-brand-red text-black"><span class="material-symbols-outlined text-[22px]">qr_code_2</span></span>
                                <div class="min-w-0">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Kode Check-In</p>
                                    <p class="mt-2 text-white font-semibold break-words">{{ $member?->checkin_code ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-6 md:p-8">
                        <div class="flex items-start gap-4">
                            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center bg-brand-red/10 border border-brand-red/35 text-brand-red"><span class="material-symbols-outlined">security</span></span>
                            <div>
                                <h3 class="font-display text-3xl uppercase italic text-white">Keamanan Akun</h3>
                                <p class="text-[#ebbbb4] mt-2 leading-relaxed">Username dan password awal diberikan kasir. Jika ingin mengganti password, gunakan menu Lupa Password di halaman login member.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>

@if($photoLimitReached)
    <div id="photo-limit-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/75 px-4">
        <div class="glass-panel w-full max-w-lg p-6 md:p-8">
            <h3 class="font-display text-3xl uppercase italic text-white mb-3">Batas Edit Foto Tercapai</h3>
            <p class="text-[#ebbbb4] mb-6">Jatah edit foto langsung sudah habis. Hubungi admin/kasir jika foto perlu diperbarui melalui persetujuan.</p>
            <button type="button" id="close-photo-limit-modal" class="btn-primary">Mengerti</button>
        </div>
    </div>
@endif

<script>
    const limitModal = document.getElementById('photo-limit-modal');
    const closeLimitModal = document.getElementById('close-photo-limit-modal');
    document.querySelectorAll('[data-photo-limit-warning]').forEach((button) => {
        button.addEventListener('click', () => {
            limitModal?.classList.remove('hidden');
            limitModal?.classList.add('flex');
        });
    });
    closeLimitModal?.addEventListener('click', () => {
        limitModal?.classList.add('hidden');
        limitModal?.classList.remove('flex');
    });
    limitModal?.addEventListener('click', (event) => {
        if (event.target === limitModal) {
            limitModal.classList.add('hidden');
            limitModal.classList.remove('flex');
        }
    });
</script>
</body>
</html>
