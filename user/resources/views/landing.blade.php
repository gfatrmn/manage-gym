<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arena Gym</title>
    <style>
        body { margin:0; font-family: Inter, system-ui, sans-serif; background:#020617; color:#f8fafc; }
        .hero { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:48px 24px; background:linear-gradient(180deg, #020617 0%, #08111f 60%); }
        .panel { max-width:1080px; width:100%; display:grid; grid-template-columns:1.1fr .9fr; gap:40px; align-items:center; }
        .content h1 { font-size:3.6rem; line-height:1.02; margin:0 0 18px; }
        .content p { max-width:640px; font-size:1.05rem; line-height:1.9; color:#94a3b8; margin:0 0 30px; }
        .buttons { display:flex; flex-wrap:wrap; gap:16px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; min-width:170px; padding:16px 22px; border-radius:14px; font-weight:700; text-decoration:none; transition:transform .2s ease, box-shadow .2s ease; }
        .btn-primary { background:#38bdf8; color:#020617; }
        .btn-secondary { background:rgba(255,255,255,.08); color:#f8fafc; border:1px solid rgba(56,189,248,.25); }
        .btn:hover { transform:translateY(-2px); box-shadow:0 18px 40px rgba(56,189,248,.18); }
        .stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; margin-top:40px; }
        .stat { background:rgba(255,255,255,.03); border:1px solid rgba(148,163,184,.12); border-radius:18px; padding:22px; }
        .stat strong { display:block; font-size:1.8rem; margin-bottom:8px; }
        .stat span { color:#94a3b8; font-size:0.95rem; }
        .hero-image { position:relative; min-height:520px; border-radius:30px; overflow:hidden; background:#111827; box-shadow:0 32px 90px rgba(0,0,0,.35); }
        .hero-image:before { content:''; position:absolute; inset:0; background:url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"%3E%3Cdefs%3E%3ClinearGradient id="g" x1="0" y1="0" x2="1" y2="1"%3E%3Cstop offset="0%25" stop-color="%2338bdf8" stop-opacity=".15"/%3E%3Cstop offset="100%25" stop-color="%2399f6e4" stop-opacity=".05"/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="800" height="800" fill="url(%23g)"/%3E%3C/svg%3E') center/cover no-repeat;
            mix-blend-mode:screen;
        }
        .hero-image .card { position:absolute; inset:24px; display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .card { background:rgba(8,18,33,.88); border:1px solid rgba(148,163,184,.12); border-radius:24px; padding:20px; }
        .card h3 { margin:0 0 12px; font-size:1.05rem; letter-spacing:.02em; text-transform:uppercase; color:#38bdf8; }
        .card p { margin:0; line-height:1.7; color:#e2e8f0; }
        .card-list { display:grid; gap:14px; margin-top:18px; }
        .card-item { display:flex; align-items:flex-start; gap:12px; }
        .bullet { width:10px; height:10px; margin-top:6px; border-radius:999px; background:#22c55e; flex-shrink:0; }
        .hero-image .member { grid-column:1/ -1; display:flex; justify-content:space-between; align-items:center; padding:20px; border-radius:24px; background:rgba(15,23,42,.94); }
        .member-info p { margin:0; color:#94a3b8; }
        .member-info strong { display:block; font-size:1.15rem; margin-bottom:6px; }
        @media (max-width:960px) { .panel { grid-template-columns:1fr; } .hero-image { min-height:420px; } }
    </style>
</head>
<body>
    <section class="hero">
        <div class="panel">
            <div class="content">
                <h1>Bangun kekuatan. Masuk lebih kuat.</h1>
                <p>Welcome ke Arena Gym — tempat latihan member dan daily pass bisa masuk, cek-in, dan mulai berlatih dengan sistem yang simpel dan cepat.</p>
                <div class="buttons">
                    <a class="btn btn-primary" href="{{ route('login') }}">Login Member</a>
                    <span class="btn btn-secondary">Daftar & Perpanjang via Kasir</span>
                </div>
                <div class="stats">
                    <div class="stat">
                        <strong>250+</strong>
                        <span>Member Aktif</span>
                    </div>
                    <div class="stat">
                        <strong>24/7</strong>
                        <span>Gym Terbuka</span>
                    </div>
                    <div class="stat">
                        <strong>18</strong>
                        <span>Kelas Mingguan</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="card">
                    <div>
                        <h3>Latihan hari ini</h3>
                        <p>Personal training, strength, dan cross-training untuk semua level. Mulai dari pemula sampai yang sudah rutin.</p>
                        <div class="card-list">
                            <div class="card-item"><span class="bullet"></span><p>Free weights & strength</p></div>
                            <div class="card-item"><span class="bullet"></span><p>Survey check-in dan member tracking</p></div>
                            <div class="card-item"><span class="bullet"></span><p>Fasilitas lengkap dan nyaman</p></div>
                        </div>
                    </div>
                    <div>
                        <h3>Temukan</h3>
                        <p>Access member area dari akun member yang sudah dibuatkan kasir, lalu gunakan lupa password jika ingin mengganti password.</p>
                        <div class="card-list">
                            <div class="card-item"><span class="bullet"></span><p>Login cepat untuk member</p></div>
                            <div class="card-item"><span class="bullet"></span><p>Pendaftaran dan perpanjangan dibantu kasir</p></div>
                        </div>
                    </div>
                </div>
                <div class="member">
                    <div class="member-info">
                        <strong>Busy Gym Session</strong>
                        <p>Hadirkan suasana latihan yang fokus, terjadwal, dan aman.</p>
                    </div>
                    <div class="member-info">
                        <strong>Mulai sekarang</strong>
                        <p>Login member, atau datang ke kasir untuk pendaftaran dan perpanjangan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
