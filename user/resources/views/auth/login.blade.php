<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Member | Arena Fitness</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=JetBrains+Mono:wght@400;600;700&family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        coal: '#0b0b0d',
                        iron: '#171719',
                        ash: '#e8e2df',
                        muted: '#b7aaa6',
                        ember: '#ff5540',
                        emberSoft: '#ffb4a8',
                    },
                    fontFamily: {
                        display: ['Oswald', 'sans-serif'],
                        body: ['Hanken Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            line-height: 1;
        }

        .gym-bg {
            background-image:
                linear-gradient(90deg, rgba(5,5,6,.94) 0%, rgba(7,7,8,.76) 42%, rgba(10,10,12,.34) 100%),
                linear-gradient(180deg, rgba(0,0,0,.24), rgba(0,0,0,.78)),
                url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=2200&q=85');
            background-size: cover;
            background-position: center;
        }

        .glass-panel {
            background: linear-gradient(180deg, rgba(28, 28, 31, .64), rgba(12, 12, 14, .52));
            border: 1px solid rgba(255, 255, 255, .16);
            box-shadow: 0 28px 80px rgba(0, 0, 0, .52), inset 0 1px 0 rgba(255, 255, 255, .08);
            backdrop-filter: blur(22px) saturate(1.2);
            -webkit-backdrop-filter: blur(22px) saturate(1.2);
        }

        .glass-field {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .14);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        }

        .glass-field:focus-within {
            border-color: rgba(255, 85, 64, .72);
            box-shadow: 0 0 0 4px rgba(255, 85, 64, .12), inset 0 1px 0 rgba(255,255,255,.06);
        }

        .scan-line {
            background: linear-gradient(90deg, transparent, rgba(255, 85, 64, .9), transparent);
            height: 1px;
            animation: sweep 3.8s ease-in-out infinite;
        }

        @keyframes sweep {
            0%, 100% { transform: translateX(-34%); opacity: .35; }
            50% { transform: translateX(34%); opacity: .9; }
        }
    </style>
</head>
<body class="min-h-screen bg-coal text-ash font-body selection:bg-ember selection:text-black">
<main class="gym-bg relative min-h-screen overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_18%,rgba(255,85,64,.22),transparent_28%),radial-gradient(circle_at_82%_72%,rgba(255,180,168,.12),transparent_24%)]"></div>
    <div class="absolute inset-x-0 top-24 scan-line"></div>

    <div class="relative z-10 min-h-screen grid grid-cols-1 lg:grid-cols-[minmax(0,0.9fr)_520px_minmax(24px,0.18fr)] items-center gap-10 px-5 py-8 md:px-14 lg:px-20">
        <section class="hidden lg:block max-w-3xl">
            <div class="inline-flex items-center gap-3 border border-white/15 bg-white/5 px-4 py-2 font-mono text-xs uppercase tracking-[0.24em] text-emberSoft backdrop-blur-md">
                <span class="h-2 w-2 rounded-full bg-ember shadow-[0_0_18px_rgba(255,85,64,.8)]"></span>
                Member Access Portal
            </div>
            <h1 class="mt-7 font-display text-[76px] leading-[.92] uppercase tracking-tight text-white drop-shadow-2xl">
                Masuk ke<br/><span class="text-ember">Arena</span><br/>Latihanmu
            </h1>
            <p class="mt-7 max-w-xl text-lg leading-8 text-muted">
                Buka QR presensi, riwayat check-in, status membership, dan notifikasi pengingat masa aktif dari satu portal member.
            </p>
            <div class="mt-10 grid max-w-xl grid-cols-3 gap-3">
                <div class="border border-white/12 bg-white/5 p-4 backdrop-blur-md">
                    <span class="material-symbols-outlined text-ember">qr_code_2</span>
                    <p class="mt-3 font-mono text-[11px] uppercase tracking-[0.18em] text-muted">QR Check-in</p>
                </div>
                <div class="border border-white/12 bg-white/5 p-4 backdrop-blur-md">
                    <span class="material-symbols-outlined text-ember">monitoring</span>
                    <p class="mt-3 font-mono text-[11px] uppercase tracking-[0.18em] text-muted">Statistik</p>
                </div>
                <div class="border border-white/12 bg-white/5 p-4 backdrop-blur-md">
                    <span class="material-symbols-outlined text-ember">notifications</span>
                    <p class="mt-3 font-mono text-[11px] uppercase tracking-[0.18em] text-muted">Reminder</p>
                </div>
            </div>
        </section>

        <section class="w-full max-w-[520px] justify-self-center lg:justify-self-start">
            <div class="glass-panel relative overflow-hidden rounded-2xl p-6 md:p-8">
                <div class="absolute right-0 top-0 h-32 w-32 translate-x-10 -translate-y-10 rounded-full bg-ember/25 blur-3xl"></div>
                <div class="relative">
                    <a href="/" class="inline-flex items-center gap-3">
                        <span class="flex h-12 w-14 items-center justify-center overflow-hidden rounded-xl border border-ember/40 bg-white/10 shadow-[0_0_28px_rgba(255,85,64,.22)]">
                            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="h-full w-full object-cover"/>
                        </span>
                        <span class="font-display text-2xl font-bold uppercase tracking-tight text-white">Arena Fitness</span>
                    </a>

                    <div class="mt-10">
                        <p class="font-mono text-xs uppercase tracking-[0.28em] text-emberSoft">Login Member</p>
                        <h2 class="mt-3 font-display text-4xl uppercase leading-tight text-white md:text-5xl">Siap Check-in?</h2>
                        <p class="mt-3 text-sm leading-6 text-muted">Gunakan username dan password dari kasir. Akun hanya aktif selama masa membership masih berlaku.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 border border-red-400/40 bg-red-500/12 px-4 py-3 text-sm text-red-100 backdrop-blur-md">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mt-6 border border-ember/40 bg-ember/12 px-4 py-3 text-sm text-emberSoft backdrop-blur-md">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('login.submit') }}" method="POST" class="mt-8 space-y-5">
                        @csrf
                        <div>
                            <label for="login" class="mb-2 block font-mono text-xs uppercase tracking-[0.18em] text-muted">Username Member</label>
                            <div class="glass-field flex items-center gap-3 rounded-xl px-4 transition">
                                <span class="material-symbols-outlined text-emberSoft/80">person</span>
                                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus autocomplete="username" placeholder="username dari kasir" class="h-14 min-w-0 flex-1 border-0 bg-transparent px-0 text-white placeholder:text-white/28 focus:ring-0"/>
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-2 block font-mono text-xs uppercase tracking-[0.18em] text-muted">Password</label>
                            <div class="glass-field flex items-center gap-3 rounded-xl px-4 transition">
                                <span class="material-symbols-outlined text-emberSoft/80">lock</span>
                                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="password member" class="h-14 min-w-0 flex-1 border-0 bg-transparent px-0 text-white placeholder:text-white/28 focus:ring-0"/>
                                <button type="button" id="togglePassword" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-muted transition hover:bg-white/10 hover:text-white" aria-label="Tampilkan password">
                                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                            <p class="font-mono text-[11px] uppercase tracking-[0.16em] text-muted">Membership aktif diperlukan</p>
                            <a href="{{ route('password.request') }}" class="font-mono text-xs uppercase tracking-[0.14em] text-emberSoft transition hover:text-white">Lupa Password?</a>
                        </div>

                        <button type="submit" class="group relative mt-2 flex h-14 w-full items-center justify-center overflow-hidden rounded-xl bg-ember px-5 font-display text-lg font-bold uppercase tracking-[0.14em] text-black transition hover:bg-white active:scale-[.99]">
                            <span class="relative z-10 flex items-center gap-2">
                                Masuk Portal
                                <span class="material-symbols-outlined text-[20px] transition group-hover:translate-x-1">arrow_forward</span>
                            </span>
                        </button>
                    </form>

                    <div class="mt-7 border-t border-white/10 pt-5">
                        <p class="text-sm leading-6 text-muted">Pendaftaran dan perpanjangan membership dilakukan langsung lewat kasir Arena Fitness.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword?.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        togglePassword.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        togglePassword.querySelector('.material-symbols-outlined').textContent = isHidden ? 'visibility' : 'visibility_off';
    });
</script>
</body>
</html>
