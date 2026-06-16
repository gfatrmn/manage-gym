<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sign In Daily Pass | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "surface-container-lowest": "#0e0e0e",
                    "secondary-fixed-dim": "#c8c6c5",
                    "primary": "#ffb4a8",
                    "on-tertiary-fixed": "#1a1c1c",
                    "primary-fixed-dim": "#ffb4a8",
                    "surface-tint": "#ffb4a8",
                    "tertiary": "#c6c6c7",
                    "on-tertiary": "#2f3131",
                    "inverse-surface": "#e2e2e2",
                    "on-background": "#e2e2e2",
                    "on-secondary-fixed-variant": "#474746",
                    "on-secondary-fixed": "#1c1b1b",
                    "secondary-fixed": "#e5e2e1",
                    "secondary-container": "#474746",
                    "error": "#ffb4ab",
                    "surface-container-high": "#2a2a2a",
                    "tertiary-container": "#909191",
                    "on-secondary-container": "#b7b5b4",
                    "on-error": "#690005",
                    "surface-dim": "#131313",
                    "surface-container-low": "#1b1b1b",
                    "background": "#131313",
                    "surface-container": "#1f1f1f",
                    "on-tertiary-container": "#282a2a",
                    "inverse-on-surface": "#303030",
                    "outline": "#b18780",
                    "tertiary-fixed-dim": "#c6c6c7",
                    "primary-fixed": "#ffdad4",
                    "on-tertiary-fixed-variant": "#454747",
                    "on-surface-variant": "#ebbbb4",
                    "inverse-primary": "#c00100",
                    "on-primary-fixed-variant": "#930100",
                    "on-primary-fixed": "#410000",
                    "on-secondary": "#313030",
                    "surface": "#131313",
                    "on-surface": "#e2e2e2",
                    "on-primary": "#690100",
                    "on-error-container": "#ffdad6",
                    "primary-container": "#ff5540",
                    "outline-variant": "#603e39",
                    "on-primary-container": "#5c0000",
                    "secondary": "#c8c6c5",
                    "tertiary-fixed": "#e2e2e2",
                    "surface-variant": "#353535",
                    "surface-bright": "#393939",
                    "error-container": "#93000a",
                    "surface-container-highest": "#353535"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "gutter": "32px",
                    "unit": "8px",
                    "margin-desktop": "64px",
                    "section-padding": "120px",
                    "margin-mobile": "20px"
            },
            "fontFamily": {
                    "display-xl": ["Oswald"],
                    "body-lg": ["Hanken Grotesk"],
                    "label-caps": ["JetBrains Mono"],
                    "headline-md": ["Oswald"],
                    "headline-lg": ["Oswald"],
                    "headline-lg-mobile": ["Oswald"],
                    "body-md": ["Hanken Grotesk"]
            },
            "fontSize": {
                    "display-xl": ["80px", {"lineHeight": "90px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "label-caps": ["14px", {"lineHeight": "20px", "letterSpacing": "0.1em", "fontWeight": "500"}],
                    "headline-md": ["32px", {"lineHeight": "38px", "fontWeight": "600"}],
                    "headline-lg": ["48px", {"lineHeight": "56px", "fontWeight": "600"}],
                    "headline-lg-mobile": ["36px", {"lineHeight": "40px", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
    <style>
        body {
            background:
                radial-gradient(circle at top right, rgba(255, 85, 64, 0.14), transparent 28%),
                linear-gradient(135deg, #050505 0%, #131313 52%, #090909 100%);
        }
        .metal-gradient {
            background: linear-gradient(135deg, #222222 0%, #111111 100%);
        }
        .red-ghost-hover:hover {
            box-shadow: 4px 4px 0px 0px #ffffff;
        }
        .input-underline {
            border-bottom: 2px solid #353535;
            transition: border-color 0.3s ease;
        }
        .input-underline:focus {
            border-color: #ff5540;
            outline: none;
        }
        .glitch-effect {
            text-shadow: 2px 0 #ff5540, -2px 0 #131313;
        }
        .industrial-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size: 44px 44px;
        }
        .field-shell {
            background: rgba(0, 0, 0, .22);
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 1rem 1.1rem;
            transition: border-color .25s ease, background .25s ease;
        }
        .field-shell:focus-within {
            border-color: rgba(255, 85, 64, .75);
            background: rgba(255, 85, 64, .05);
        }
    </style>
</head>
<body class="text-on-background font-body-md selection:bg-primary-container selection:text-white">
<main class="min-h-screen grid grid-cols-1 lg:grid-cols-2 industrial-grid">
    <section class="relative hidden lg:flex min-h-screen overflow-hidden bg-black items-center justify-center border-r border-surface-variant">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover opacity-55 filter grayscale contrast-125" src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1400&q=85" alt="Arena Fitness training floor"/>
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/65 to-black/15"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
        </div>
        <div class="absolute top-8 left-8 font-label-caps text-label-caps text-primary uppercase tracking-[0.3em]">Arena Fitness</div>
        <div class="relative z-10 p-margin-desktop text-left max-w-xl">
            <span class="font-label-caps text-label-caps text-primary-container uppercase tracking-[0.35em] block mb-5">Daily Pass Access</span>
            <h2 class="font-display-xl text-[92px] text-white uppercase tracking-tighter leading-[0.88] italic">Mulai <br/> Dari <br/> Sini</h2>
            <div class="mt-8 h-1 w-24 bg-primary-container"></div>
            <p class="mt-6 font-label-caps text-label-caps text-white/70 max-w-md">Buat akun daily pass, pilih membership, upload bukti pembayaran, lalu masuk ke arena setelah diverifikasi.</p>
        </div>
    </section>

    <section class="flex min-h-screen px-margin-mobile md:px-margin-desktop py-12 md:py-16 items-center justify-center">
        <div class="w-full max-w-xl">
            <div class="mb-8 flex items-center justify-between gap-4">
                <a class="font-headline-md text-3xl font-bold text-primary tracking-tighter uppercase italic" href="/">Arena Fitness</a>
                <a class="font-label-caps text-xs text-on-surface-variant hover:text-primary uppercase transition-colors" href="{{ route('login') }}">Login</a>
            </div>

            <div class="metal-gradient border border-white/10 p-6 md:p-10 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 opacity-10">
                    <span class="material-symbols-outlined text-[180px] text-primary-container">fitness_center</span>
                </div>

                <div class="relative z-10 mb-10">
                    <span class="font-label-caps text-xs text-primary-container uppercase tracking-[0.3em]">Sign In Daily Pass</span>
                    <h1 class="font-display-xl text-headline-lg-mobile md:text-headline-lg uppercase mt-4 mb-3 italic glitch-effect">Buat Akses Arena</h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant">Daftar sebagai daily pass untuk mulai proses membership dan pembayaran.</p>
                </div>

                @if ($errors->any())
                    <div class="relative z-10 mb-6 p-4 bg-error/20 border border-error/40 text-error text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form class="relative z-10 space-y-6" method="POST" action="{{ route('signup.submit') }}">
                    @csrf
                    <div class="grid grid-cols-1 gap-5">
                        <div class="field-shell">
                            <label class="font-label-caps text-xs text-on-surface-variant block mb-2 uppercase">Nama Lengkap</label>
                            <input class="w-full bg-transparent border-0 p-0 font-label-caps text-on-background placeholder:opacity-30 focus:ring-0" name="full_name" placeholder="LEONIDAS BOLD" type="text" value="{{ old('full_name') }}" required/>
                        </div>
                        <div class="field-shell">
                            <label class="font-label-caps text-xs text-on-surface-variant block mb-2 uppercase">Nomor HP</label>
                            <input class="w-full bg-transparent border-0 p-0 font-label-caps text-on-background placeholder:opacity-30 focus:ring-0" name="phone" placeholder="+62 8XX XXXX XXXX" type="text" value="{{ old('phone') }}" required/>
                        </div>
                        <div class="field-shell">
                            <label class="font-label-caps text-xs text-on-surface-variant block mb-2 uppercase">Email</label>
                            <input class="w-full bg-transparent border-0 p-0 font-label-caps text-on-background placeholder:opacity-30 focus:ring-0" name="email" placeholder="WARRIOR@ARENAFITNESS.COM" type="email" value="{{ old('email') }}" required/>
                        </div>
                        <div class="field-shell">
                            <label class="font-label-caps text-xs text-on-surface-variant block mb-2 uppercase">Password</label>
                            <div class="relative">
                                <input id="signup-password" class="w-full bg-transparent border-0 p-0 pr-10 font-label-caps text-on-background placeholder:opacity-30 focus:ring-0" name="password" placeholder="MINIMAL 8 KARAKTER" type="password" required/>
                                <button type="button" class="toggle-password material-symbols-outlined absolute right-0 top-0 text-on-surface-variant/70 hover:text-primary transition-colors" data-target="signup-password">visibility_off</button>
                            </div>
                        </div>
                        <div class="field-shell">
                            <label class="font-label-caps text-xs text-on-surface-variant block mb-2 uppercase">Konfirmasi Password</label>
                            <div class="relative">
                                <input id="signup-password-confirmation" class="w-full bg-transparent border-0 p-0 pr-10 font-label-caps text-on-background placeholder:opacity-30 focus:ring-0" name="password_confirmation" placeholder="ULANGI PASSWORD" type="password" required/>
                                <button type="button" class="toggle-password material-symbols-outlined absolute right-0 top-0 text-on-surface-variant/70 hover:text-primary transition-colors" data-target="signup-password-confirmation">visibility_off</button>
                            </div>
                        </div>
                    </div>

                    <button class="w-full bg-primary-container text-black font-headline-md text-2xl py-5 uppercase tracking-wider red-ghost-hover transition-all transform active:scale-95 flex items-center justify-center gap-3" type="submit">
                        Buat Akun
                        <span class="material-symbols-outlined">trending_flat</span>
                    </button>
                </form>

                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center gap-4 pt-8">
                    <p class="font-label-caps text-xs text-on-surface-variant uppercase">Sudah punya akun?</p>
                    <a class="font-label-caps text-xs text-primary underline underline-offset-4 hover:text-white transition-colors uppercase" href="{{ route('login') }}">Login ke Dashboard</a>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    document.querySelectorAll('.toggle-password').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'visibility' : 'visibility_off';
        });
    });
</script>
</body>
</html>
