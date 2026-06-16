<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Membership | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet"/>
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
                        'on-surface-variant': '#ebbbb4',
                        primary: '#ffb4a8',
                        'brand-red': '#ff5540',
                    },
                    fontFamily: {
                        display: ['Oswald'],
                        mono: ['JetBrains Mono'],
                        body: ['Hanken Grotesk'],
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
        }

        .glass-panel {
            background: linear-gradient(135deg, rgba(31,31,31,.74), rgba(14,14,14,.52));
            border: 1px solid rgba(255,255,255,.13);
            box-shadow: 0 24px 80px rgba(0,0,0,.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .glass-tile {
            background: rgba(255,255,255,.055);
            border: 1px solid rgba(255,255,255,.11);
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

        .btn-primary {
            background: #ff5540;
            color: #080808;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;
            padding: 1rem 1.35rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            letter-spacing: .18em;
            text-transform: uppercase;
            font-weight: 700;
            transition: all .25s ease;
        }

        .btn-primary:hover {
            background: #fff;
            color: #ff5540;
            transform: translateY(-2px);
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
    </style>
    <link href="{{ asset('css/member-responsive.css') }}?v={{ file_exists(public_path('css/member-responsive.css')) ? filemtime(public_path('css/member-responsive.css')) : time() }}" rel="stylesheet"/>
</head>
<body class="bg-[#131313] text-[#e2e2e2] font-body selection:bg-brand-red selection:text-black">
@include('member.partials.page-loader')
@php
    $planName = $member?->membership_plan ?: 'Arena Member';
    $joinedAt = $member?->joined_at;
    $expiresAt = $member?->expires_at;
    $statusLabel = strtoupper(str_replace('_', ' ', $membershipStatus ?? 'member'));
    $daysLabel = $remainingDays === null ? '-' : $remainingDays;
    $progressValue = min(100, max(0, (int) ($progressPercent ?? 0)));
    $latestPaymentDate = $latestPayment?->transaction_at ? $latestPayment->transaction_at->format('d M Y') : '-';
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
            <a class="nav-link active text-brand-red" href="{{ route('membership') }}">Membership</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('profile') }}">Profil</a>
            <a class="nav-link text-[#ebbbb4] hover:text-brand-red" href="{{ route('history') }}">Riwayat</a>
        </nav>
        <div class="flex items-center gap-3 justify-self-end">
            @include('member.partials.notifications')
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 font-mono text-xs border border-white/10 bg-white/5 text-white px-4 py-3 uppercase tracking-[0.18em] hover:border-brand-red hover:text-brand-red transition-colors">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<main class="pt-20">
    @if(session('status'))
        <div class="max-w-screen-2xl mx-auto px-5 md:px-16 pt-4">
            <div class="border border-green-500/30 bg-green-500/10 px-5 py-4 text-green-300">
                {{ session('status') }}
            </div>
        </div>
    @endif

    <section class="relative overflow-hidden metal-grid border-b border-white/10">
        <div class="absolute inset-0">
            <img alt="Arena Fitness membership" class="h-full w-full object-cover grayscale brightness-[0.35] contrast-125" src="https://images.unsplash.com/photo-1534258936925-c58bed479fcb?auto=format&fit=crop&w=2200&q=85" onerror="this.style.display='none'"/>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/85 to-black/35"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#131313] via-transparent to-transparent"></div>
        </div>

        <div class="relative max-w-screen-2xl mx-auto px-5 md:px-16 py-12 md:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <div class="lg:col-span-7 flex flex-col justify-center">
                    <div class="inline-flex w-fit items-center gap-3 glass-tile px-4 py-2 mb-6">
                        <span class="h-2 w-2 bg-brand-red shadow-[0_0_14px_#ff5540]"></span>
                        <span class="font-mono text-[11px] uppercase tracking-[0.28em] text-[#ffb4a8]">Membership Active</span>
                    </div>
                    <h1 class="font-display text-[52px] md:text-[86px] uppercase italic leading-[0.92] mb-6 text-white">
                        {{ strtoupper($planName) }}
                    </h1>
                    <p class="text-[#ebbbb4] text-lg md:text-xl max-w-2xl leading-relaxed">
                        Pantau status membership, masa aktif, dan pembayaran terakhir dari satu halaman yang lebih jelas dan terpusat. Perpanjangan membership tetap dilayani melalui kasir Arena Fitness.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a class="btn-primary" href="{{ route('barcode') }}">
                            <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                            Buka QR Check-In
                        </a>
                        <a class="glass-tile inline-flex items-center justify-center gap-3 px-5 py-4 font-mono text-xs uppercase tracking-[0.18em] text-white hover:text-brand-red" href="{{ route('history') }}">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                            Riwayat Latihan
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="glass-panel p-6 md:p-8 h-full flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-4 mb-8">
                                <div>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.24em] text-[#ebbbb4]">Arena Access Card</p>
                                    <h2 class="font-display text-3xl uppercase italic text-white mt-2">{{ $statusLabel }}</h2>
                                </div>
                                <span class="inline-flex h-12 w-12 items-center justify-center border border-brand-red/40 bg-brand-red/10 text-brand-red">
                                    <span class="material-symbols-outlined">workspace_premium</span>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-8">
                                <div class="glass-tile p-4">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Sisa Aktif</p>
                                    <p class="font-display text-5xl text-brand-red mt-1">{{ $daysLabel }}<span class="text-base text-white">H</span></p>
                                </div>
                                <div class="glass-tile p-4">
                                    <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Berakhir</p>
                                    <p class="font-display text-2xl text-white mt-3">{{ $expiresAt ? $expiresAt->format('d M Y') : '-' }}</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Progress Masa Aktif</span>
                                    <span class="font-mono text-xs text-brand-red">{{ $progressValue }}%</span>
                                </div>
                                <div class="h-3 bg-white/10 overflow-hidden">
                                    <div class="h-full bg-brand-red" style="width: {{ $progressValue }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-white/10 pt-5">
                            <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4] mb-2">Perpanjangan</p>
                            <p class="text-sm text-[#ebbbb4] leading-relaxed">Silakan datang ke kasir untuk memperpanjang masa aktif membership.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#131313] px-5 md:px-16 py-10 md:py-14">
        <div class="max-w-screen-2xl mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="glass-tile p-5">
                <span class="material-symbols-outlined text-brand-red mb-4">event_available</span>
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Tanggal Mulai</p>
                <p class="font-display text-2xl text-white mt-2">{{ $joinedAt ? $joinedAt->format('d M Y') : '-' }}</p>
            </div>
            <div class="glass-tile p-5">
                <span class="material-symbols-outlined text-brand-red mb-4">event_busy</span>
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Berlaku Sampai</p>
                <p class="font-display text-2xl text-white mt-2">{{ $expiresAt ? $expiresAt->format('d M Y') : '-' }}</p>
            </div>
            <div class="glass-tile p-5">
                <span class="material-symbols-outlined text-brand-red mb-4">payments</span>
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Biaya Terakhir</p>
                <p class="font-display text-2xl text-white mt-2">IDR {{ number_format((int) ($latestPayment?->amount ?? 0), 0, ',', '.') }}</p>
            </div>
            <div class="glass-tile p-5">
                <span class="material-symbols-outlined text-brand-red mb-4">credit_card</span>
                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Pembayaran Terakhir</p>
                <p class="font-display text-2xl text-white mt-2">{{ $latestPaymentDate }}</p>
            </div>
        </div>
    </section>

    <section class="bg-[#0e0e0e] px-5 md:px-16 py-12 md:py-16 border-y border-white/10">
        <div class="max-w-screen-2xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 glass-panel p-6 md:p-8">
                <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">Detail Akses</span>
                <h2 class="font-display text-4xl uppercase italic text-white mt-3 mb-6">Membership Info</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <span class="text-[#ebbbb4]">Nama Member</span>
                        <span class="text-white font-semibold text-right">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <span class="text-[#ebbbb4]">Paket</span>
                        <span class="text-white font-semibold text-right">{{ $planName }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <span class="text-[#ebbbb4]">Metode Bayar</span>
                        <span class="text-white font-semibold text-right">{{ strtoupper($latestPayment?->payment_method ?? $member?->payment_method ?? '-') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-[#ebbbb4]">Status Akun</span>
                        <span class="font-mono text-xs uppercase tracking-[0.16em] text-brand-red">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 glass-panel overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 border-b border-white/10">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.24em] text-brand-red">Billing History</span>
                        <h2 class="font-display text-3xl uppercase italic text-white mt-2">Riwayat Pembayaran</h2>
                    </div>
                    <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-[#ebbbb4]">Kasir Arena Fitness</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse">
                        <thead>
                        <tr class="border-b border-white/10 bg-white/5">
                            <th class="px-6 py-4 font-mono text-[#ebbbb4] uppercase text-[10px] tracking-[0.16em]">Invoice</th>
                            <th class="px-6 py-4 font-mono text-[#ebbbb4] uppercase text-[10px] tracking-[0.16em]">Tanggal</th>
                            <th class="px-6 py-4 font-mono text-[#ebbbb4] uppercase text-[10px] tracking-[0.16em]">Paket</th>
                            <th class="px-6 py-4 font-mono text-[#ebbbb4] uppercase text-[10px] tracking-[0.16em]">Jumlah</th>
                            <th class="px-6 py-4 font-mono text-[#ebbbb4] uppercase text-[10px] tracking-[0.16em] text-center">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                        @forelse($paymentHistory as $payment)
                            @php
                                $status = strtolower($payment->payment_status ?? 'verified');
                                $isSuccess = in_array($status, ['verified', 'paid', 'success', 'berhasil'], true);
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-5 font-mono text-white text-xs">{{ $payment->invoice ?? '#TRX-' . $payment->id }}</td>
                                <td class="px-6 py-5 text-[#ebbbb4]">{{ $payment->transaction_at ? $payment->transaction_at->format('d M Y') : '-' }}</td>
                                <td class="px-6 py-5 text-white">{{ $payment->transaction_type ?? $payment->type ?? $payment->description ?? 'Membership' }}</td>
                                <td class="px-6 py-5 text-white">IDR {{ number_format((int) $payment->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="{{ $isSuccess ? 'bg-green-500/10 text-green-300 border-green-500/20' : 'bg-red-500/10 text-red-300 border-red-500/20' }} border px-3 py-1 font-mono text-[10px] uppercase tracking-[0.14em]">
                                        {{ $isSuccess ? 'Berhasil' : ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-[#ebbbb4]">
                                    Belum ada riwayat pembayaran membership.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-black border-t border-white/10 w-full py-10">
    <div class="px-5 md:px-16 max-w-screen-2xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
        <div class="font-display text-brand-red text-3xl uppercase italic">Arena Fitness</div>
        <p class="font-mono text-[10px] text-[#ebbbb4]/60 tracking-[0.18em] uppercase">&copy; 2024 Arena Fitness. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
