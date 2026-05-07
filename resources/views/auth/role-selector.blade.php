<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|space-grotesk:500,700" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --bg: #060606;
                --panel: rgba(16, 16, 16, 0.94);
                --border: rgba(255,255,255,0.08);
                --text: #f3f3f3;
                --muted: #acacb3;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background:
                    radial-gradient(circle at top right, rgba(255, 59, 59, 0.2), transparent 28%),
                    radial-gradient(circle at bottom left, rgba(166, 15, 31, 0.24), transparent 32%),
                    linear-gradient(150deg, #030303 0%, #0b0b0b 52%, #17080b 100%);
                color: var(--text);
            }

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                pointer-events: none;
                background:
                    linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
                background-size: 44px 44px;
            }

            .portal-card {
                border: 1px solid var(--border);
                border-radius: 1.75rem;
                box-shadow: 0 28px 60px rgba(0, 0, 0, 0.4);
                background: linear-gradient(180deg, var(--panel) 0%, rgba(8, 8, 8, 0.98) 100%);
                transition: .25s ease;
            }

            .portal-card:hover {
                transform: translateY(-4px);
                border-color: rgba(255, 59, 59, 0.22);
            }

            .eyebrow {
                letter-spacing: .2em;
                text-transform: uppercase;
                color: #ff9898;
                font-size: .8rem;
                font-weight: 700;
            }

            .hero-copy {
                color: var(--muted);
                max-width: 42rem;
            }

            .text-secondary {
                color: var(--muted) !important;
            }

            .btn-dark {
                background: linear-gradient(135deg, #ff5050 0%, #b10018 100%);
                border-color: #cf2336;
                box-shadow: 0 16px 30px rgba(177, 0, 24, 0.35);
            }

            .btn-outline-dark {
                color: #fff;
                border-color: rgba(255,255,255,0.18);
            }

            .btn-outline-dark:hover {
                background: rgba(255,255,255,0.06);
                color: #fff;
                border-color: rgba(255,255,255,0.24);
            }

            .role-accent {
                width: 3rem;
                height: 3rem;
                border-radius: 1rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #ff5656 0%, #9f0619 100%);
                font-family: 'Space Grotesk', sans-serif;
                font-weight: 700;
                box-shadow: 0 18px 28px rgba(159, 6, 25, 0.35);
            }
        </style>
    </head>
    <body class="d-flex align-items-center min-vh-100">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="text-center mb-4">
                        <div class="eyebrow mb-3">Arena Gym Entry Point</div>
                        <h1 class="display-5 fw-bold">Portal Login Arena Gym</h1>
                        <p class="hero-copy mx-auto">Pilih akses masuk sesuai peran untuk memisahkan area admin dan area kasir dengan tampilan dominan hitam dan merah yang lebih berkarakter.</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="portal-card p-4 h-100">
                                <div class="role-accent">AD</div>
                                <div class="text-uppercase small fw-bold text-secondary mt-4">Admin Area</div>
                                <h2 class="h3 fw-bold mt-3">Kelola operasional Arena Gym</h2>
                                <p class="text-secondary">Akses dashboard admin, data member, check-in, membership, pengumuman, dan laporan harian.</p>
                                <a href="{{ route('admin.login') }}" class="btn btn-dark rounded-pill px-4">Login Admin</a>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="portal-card p-4 h-100">
                                <div class="role-accent">MA</div>
                                <div class="text-uppercase small fw-bold text-secondary mt-4">Master Admin</div>
                                <h2 class="h3 fw-bold mt-3">Akses admin dan kasir</h2>
                                <p class="text-secondary">Satu akun dengan akses penuh ke dashboard admin sekaligus seluruh modul kasir.</p>
                                <a href="{{ route('master-admin.login') }}" class="btn btn-dark rounded-pill px-4">Login Master Admin</a>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="portal-card p-4 h-100">
                                <div class="role-accent">KS</div>
                                <div class="text-uppercase small fw-bold text-secondary mt-4">Kasir Area</div>
                                <h2 class="h3 fw-bold mt-3">Kelola transaksi kasir</h2>
                                <p class="text-secondary">Akses dashboard kasir untuk transaksi, pembayaran, ringkasan penjualan, dan shift harian.</p>
                                <a href="{{ route('cashier.login') }}" class="btn btn-outline-dark rounded-pill px-4">Login Kasir</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
