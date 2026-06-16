<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Arena Gym — Intro 2</title>
    <style>
        body{margin:0;font-family:Inter,system-ui,Arial;background:#081026;color:#fff}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px}
        .card{max-width:1000px;width:100%;display:grid;grid-template-columns:360px 1fr;gap:28px;align-items:center}
        .right h1{font-size:2.4rem;margin:0 0 12px}
        .right p{color:#cbd5e1;margin:0 0 18px}
        .nav{display:flex;gap:12px;align-items:center}
        .arrow{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:999px;border:1px solid rgba(255,255,255,.08);background:transparent;color:#fff;text-decoration:none}
        .panel{background:rgba(15,23,42,.7);padding:22px;border-radius:16px;border:1px solid rgba(148,163,184,.06)}
        .cta{display:inline-block;padding:12px 18px;border-radius:12px;background:#10b981;color:#02111a;font-weight:700;text-decoration:none}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="panel">
                <h3>Kelas & Trainer</h3>
                <p>Ikuti kelas mingguan atau book trainer pribadi untuk rencana latihan yang dipersonalisasi.</p>
            </div>
            <div class="right">
                <h1>Komunitas & Support</h1>
                <p>Latih bersama teman-teman, dapatkan dukungan, dan capai target lebih cepat dengan program kami.</p>
                <div class="nav">
                    <a class="arrow" href="{{ route('landing.step1') }}">←</a>
                    <a class="arrow" href="{{ route('landing.step3') }}">→</a>
                    <a class="cta" href="{{ route('landing.step3') }}">Lihat Selanjutnya</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
