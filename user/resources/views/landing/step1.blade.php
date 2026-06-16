<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Arena Gym — Intro 1</title>
    <style>
        body{margin:0;font-family:Inter,system-ui,Arial;background:#020617;color:#fff}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px}
        .card{max-width:1000px;width:100%;display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:center}
        .left h1{font-size:2.8rem;margin:0 0 12px}
        .left p{color:#94a3b8;margin:0 0 18px}
        .nav{display:flex;gap:12px;align-items:center}
        .arrow{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:999px;border:1px solid rgba(255,255,255,.08);background:transparent;color:#fff;text-decoration:none}
        .panel{background:linear-gradient(180deg,#08111f,#071025);padding:22px;border-radius:16px;border:1px solid rgba(148,163,184,.06)}
        .cta{display:inline-block;padding:12px 18px;border-radius:12px;background:#38bdf8;color:#020617;font-weight:700;text-decoration:none}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="left">
                <h1>Selamat datang di Arena Gym</h1>
                <p>Rasakan pengalaman latihan yang terstruktur: alat lengkap, trainer siap bantu, dan sistem check-in cepat untuk member maupun pengunjung.</p>
                <div class="nav">
                    <a class="arrow" href="{{ route('landing.step2') }}">→</a>
                    <a class="cta" href="{{ route('landing.step2') }}">Lanjutkan</a>
                </div>
            </div>
            <div class="panel">
                <h3>Fokus Hari Ini</h3>
                <p>Strength, conditioning, dan class terjadwal — semua tersedia kapan saja.</p>
            </div>
        </div>
    </div>
</body>
</html>
