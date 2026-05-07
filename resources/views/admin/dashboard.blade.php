<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Admin Dashboard Gym</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|manrope:600,700,800" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --bg: #eef2f6;
                --bg-deep: #dfe6ee;
                --surface: rgba(255, 255, 255, 0.8);
                --surface-strong: #ffffff;
                --ink: #17202d;
                --panel: #111827;
                --muted: #697586;
                --line: rgba(111, 122, 140, 0.18);
                --line-strong: rgba(111, 122, 140, 0.28);
                --brand: #0f766e;
                --brand-soft: rgba(15, 118, 110, 0.12);
                --accent: #f97316;
                --accent-soft: rgba(249, 115, 22, 0.13);
                --success: #16a34a;
                --success-soft: rgba(22, 163, 74, 0.12);
                --warning: #d97706;
                --shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(15, 118, 110, 0.12), transparent 24%),
                    radial-gradient(circle at right top, rgba(249, 115, 22, 0.12), transparent 20%),
                    linear-gradient(180deg, #f8fafc 0%, var(--bg) 42%, var(--bg-deep) 100%);
                color: var(--ink);
            }

            .display-font {
                font-family: 'Manrope', sans-serif;
            }

            .glass-panel {
                background: var(--surface);
                border: 1px solid rgba(255, 255, 255, 0.75);
                box-shadow: var(--shadow);
                backdrop-filter: blur(18px);
            }

            .mesh {
                position: relative;
                overflow: hidden;
            }

            .mesh::before {
                content: '';
                position: absolute;
                inset: auto -15% -35% auto;
                width: 15rem;
                height: 15rem;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(15, 118, 110, 0.14), transparent 68%);
            }

            .dark-panel {
                background: linear-gradient(180deg, #172131 0%, #0f172a 100%);
                color: white;
            }

            .eyebrow {
                letter-spacing: 0.18em;
                text-transform: uppercase;
                font-size: 0.73rem;
                font-weight: 700;
            }

            .soft-table {
                background: rgba(255, 255, 255, 0.88);
                border: 1px solid var(--line);
            }

            .metric-pill {
                background: var(--brand-soft);
                color: var(--brand);
            }

            .rise-in {
                animation: rise-in 0.7s ease-out both;
            }

            .rise-delay-1 {
                animation-delay: 0.08s;
            }

            .rise-delay-2 {
                animation-delay: 0.16s;
            }

            .rise-delay-3 {
                animation-delay: 0.24s;
            }

            @keyframes rise-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body class="min-h-screen">
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-5 sm:px-6 lg:px-8">
            <header class="glass-panel rise-in mb-5 rounded-[32px] border border-white/70 px-5 py-5 sm:px-7 lg:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-4 flex flex-wrap items-center gap-3">
                            <p class="eyebrow inline-flex rounded-full bg-[var(--panel)] px-3 py-1.5 text-white">
                                Iron Pulse Admin
                            </p>
                            <p class="inline-flex rounded-full border border-[var(--line)] bg-white/80 px-3 py-1.5 text-sm font-semibold text-[var(--muted)]">
                                Operasional Hari Ini Stabil
                            </p>
                        </div>
                        <h1 class="display-font text-3xl font-extrabold leading-tight tracking-[-0.03em] sm:text-4xl lg:text-[3.25rem]">
                            Dashboard operasional gym yang lebih profesional dan nyaman dipakai tim admin.
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-[var(--muted)] sm:text-base">
                            Tampilan dirapikan untuk membantu pemindaian cepat saat jam sibuk, dengan hierarchy yang lebih jelas untuk KPI, kelas, trainer, dan tindak lanjut operasional.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3 lg:w-[23rem] lg:grid-cols-1">
                        <div class="dark-panel rounded-[24px] px-5 py-4">
                            <p class="eyebrow text-white/50">Jam Operasional</p>
                            <p class="mt-3 text-2xl font-bold">05:30 - 22:00</p>
                            <p class="mt-1 text-sm text-white/70">Front desk, studio, personal training</p>
                        </div>
                        <div class="rounded-[24px] border border-[var(--line)] bg-[var(--surface-strong)] px-5 py-4">
                            <p class="eyebrow text-[var(--muted)]">Target Harian</p>
                            <p class="mt-3 text-2xl font-bold">74%</p>
                            <p class="mt-1 text-sm text-[var(--muted)]">Revenue dan check-in on track</p>
                        </div>
                        <div class="rounded-[24px] border border-[rgba(249,115,22,0.2)] bg-[rgba(255,247,237,0.92)] px-5 py-4">
                            <p class="eyebrow text-[var(--warning)]">Floor Status</p>
                            <p class="mt-3 text-2xl font-bold">Peak Hours</p>
                            <p class="mt-1 text-sm text-[var(--warning)]">Free weight hampir penuh</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="grid flex-1 gap-5 lg:grid-cols-[1.3fr_0.7fr]">
                <section class="grid gap-5">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($stats as $index => $stat)
                            <article class="glass-panel mesh rise-in rounded-[28px] border border-white/70 p-5 {{ $index === 1 ? 'rise-delay-1' : ($index === 2 ? 'rise-delay-2' : ($index === 3 ? 'rise-delay-3' : '')) }}">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold text-[var(--muted)]">{{ $stat['label'] }}</p>
                                    <span class="metric-pill rounded-full px-3 py-1 text-xs font-bold">{{ $stat['change'] }}</span>
                                </div>
                                <div class="mt-6 flex items-end justify-between gap-3">
                                    <h2 class="display-font text-3xl font-extrabold tracking-[-0.03em]">{{ $stat['value'] }}</h2>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-[var(--muted)]">{{ $stat['note'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="grid gap-5 xl:grid-cols-[1.2fr_0.8fr]">
                        <section class="glass-panel rise-in rounded-[30px] border border-white/70 p-5 sm:p-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="eyebrow text-[var(--muted)]">Class Management</p>
                                    <h2 class="display-font mt-2 text-2xl font-extrabold tracking-[-0.03em]">Jadwal studio dan okupansi real-time</h2>
                                </div>
                                <button class="rounded-full bg-[var(--brand)] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0b5f59]">
                                    Tambah kelas
                                </button>
                            </div>

                            <div class="soft-table mt-6 overflow-hidden rounded-[26px]">
                                <div class="hidden grid-cols-[0.7fr_1.35fr_1fr_1fr] gap-4 border-b border-[var(--line)] bg-slate-50/90 px-5 py-4 text-xs font-extrabold uppercase tracking-[0.18em] text-[var(--muted)] md:grid">
                                    <span>Waktu</span>
                                    <span>Kelas</span>
                                    <span>Pelatih</span>
                                    <span>Kapasitas</span>
                                </div>
                                @foreach ($schedule as $item)
                                    @php
                                        [$filled, $capacity] = array_map('intval', explode('/', $item['slots']));
                                        $occupancy = $capacity > 0 ? round(($filled / $capacity) * 100) : 0;
                                    @endphp
                                    <article class="grid gap-4 border-b border-[var(--line)] px-5 py-4 last:border-b-0 md:grid-cols-[0.7fr_1.35fr_1fr_1fr] md:items-center">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[var(--panel)] text-sm font-bold text-white">
                                                {{ $item['time'] }}
                                            </div>
                                            <div class="md:hidden">
                                                <h3 class="text-lg font-bold">{{ $item['class'] }}</h3>
                                                <p class="text-sm text-[var(--muted)]">{{ $item['coach'] }}</p>
                                            </div>
                                        </div>

                                        <div class="hidden md:block">
                                            <h3 class="text-base font-bold">{{ $item['class'] }}</h3>
                                            <p class="text-sm text-[var(--muted)]">Kelas aktif untuk peak member traffic</p>
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-[var(--ink)]">{{ $item['coach'] }}</p>
                                            <p class="text-sm text-[var(--muted)]">Studio coach on duty</p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div class="h-2.5 w-24 rounded-full bg-slate-200">
                                                <div class="h-2.5 rounded-full bg-[var(--brand)]" style="width: {{ $occupancy }}%"></div>
                                            </div>
                                            <span class="rounded-full bg-[var(--brand-soft)] px-3 py-1 text-sm font-semibold text-[var(--brand)]">
                                                {{ $item['slots'] }} slot
                                            </span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>

                        <section class="glass-panel rise-in rise-delay-1 rounded-[30px] border border-white/70 p-5 sm:p-6">
                            <p class="eyebrow text-[var(--muted)]">Operational Alerts</p>
                            <h2 class="display-font mt-2 text-2xl font-extrabold tracking-[-0.03em]">Hal yang perlu ditindak hari ini</h2>

                            <div class="mt-6 space-y-3">
                                @foreach ($alerts as $alert)
                                    <article class="rounded-[24px] border border-[var(--line)] bg-white/88 p-4">
                                        <div class="flex items-start gap-3">
                                            <span class="mt-1 h-3 w-3 rounded-full bg-[var(--accent)]"></span>
                                            <p class="text-sm leading-6 text-[var(--ink)]">{{ $alert }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mt-6 rounded-[24px] border border-[rgba(22,163,74,0.18)] bg-[var(--success-soft)] p-5">
                                <p class="eyebrow text-[var(--success)]">Retention Insight</p>
                                <p class="mt-3 text-4xl font-bold">92%</p>
                                <p class="mt-2 text-sm leading-6 text-[var(--muted)]">
                                    Renewal paket tahunan sedang naik. Fokuskan follow-up ke member yang pasif lebih dari 10 hari.
                                </p>
                            </div>
                        </section>
                    </div>
                </section>

                <aside class="grid gap-5">
                    <section class="glass-panel rise-in rise-delay-2 rounded-[30px] border border-white/70 p-5 sm:p-6">
                        <p class="eyebrow text-[var(--muted)]">Area Utilization</p>
                        <h2 class="display-font mt-2 text-2xl font-extrabold tracking-[-0.03em]">Kepadatan area gym</h2>

                        <div class="mt-6 space-y-4">
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span>Free Weight</span>
                                    <span class="font-bold text-[var(--warning)]">89%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-200"><div class="h-3 rounded-full bg-[var(--warning)]" style="width: 89%"></div></div>
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span>Cardio Deck</span>
                                    <span class="font-bold text-[var(--brand)]">61%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-200"><div class="h-3 rounded-full bg-[var(--brand)]" style="width: 61%"></div></div>
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span>Functional Turf</span>
                                    <span class="font-bold text-sky-600">74%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-200"><div class="h-3 rounded-full bg-sky-500" style="width: 74%"></div></div>
                            </div>
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span>Recovery Lounge</span>
                                    <span class="font-bold text-[var(--success)]">46%</span>
                                </div>
                                <div class="h-3 rounded-full bg-slate-200"><div class="h-3 rounded-full bg-[var(--success)]" style="width: 46%"></div></div>
                            </div>
                        </div>
                    </section>

                    <section class="glass-panel rise-in rise-delay-3 rounded-[30px] border border-white/70 p-5 sm:p-6">
                        <div class="flex items-end justify-between gap-3">
                            <div>
                                <p class="eyebrow text-[var(--muted)]">Trainer Performance</p>
                                <h2 class="display-font mt-2 text-2xl font-extrabold tracking-[-0.03em]">Sesi paling produktif minggu ini</h2>
                            </div>
                            <span class="rounded-full bg-[var(--brand-soft)] px-3 py-1 text-xs font-bold text-[var(--brand)]">Minggu Ini</span>
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach ($trainers as $trainer)
                                <article class="rounded-[24px] border border-[var(--line)] bg-white/88 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-base font-bold">{{ $trainer['name'] }}</h3>
                                            <p class="text-sm text-[var(--muted)]">{{ $trainer['focus'] }}</p>
                                        </div>
                                        <span class="rounded-full bg-[var(--success-soft)] px-3 py-1 text-sm font-semibold text-[var(--success)]">{{ $trainer['score'] }}</span>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between text-sm">
                                        <span class="text-[var(--muted)]">Sesi aktif</span>
                                        <span class="font-bold text-[var(--ink)]">{{ $trainer['sessions'] }} sesi</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section class="dark-panel rise-in rounded-[30px] p-5 text-white shadow-[0_24px_50px_rgba(20,14,10,0.28)] sm:p-6">
                        <p class="eyebrow text-white/50">Quick Actions</p>
                        <h2 class="display-font mt-2 text-2xl font-extrabold tracking-[-0.03em]">Tindakan cepat admin</h2>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                            <button class="rounded-[22px] bg-white px-4 py-4 text-left text-[var(--ink)] transition hover:-translate-y-0.5">
                                <span class="block text-sm text-[var(--muted)]">Membership</span>
                                <span class="mt-1 block text-lg font-bold">Buat invoice perpanjangan</span>
                            </button>
                            <button class="rounded-[22px] border border-white/10 bg-white/10 px-4 py-4 text-left text-white transition hover:bg-white/15">
                                <span class="block text-sm text-white/55">Personal Training</span>
                                <span class="mt-1 block text-lg font-bold">Atur jadwal trainer</span>
                            </button>
                            <button class="rounded-[22px] border border-white/10 bg-white/10 px-4 py-4 text-left text-white transition hover:bg-white/15">
                                <span class="block text-sm text-white/55">Equipment</span>
                                <span class="mt-1 block text-lg font-bold">Catat maintenance alat</span>
                            </button>
                        </div>
                    </section>
                </aside>
            </main>
        </div>
    </body>
</html>
