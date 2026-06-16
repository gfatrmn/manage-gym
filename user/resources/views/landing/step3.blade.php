<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Arena Gym — Intro 3</title>
    <style>
        body{margin:0;font-family:Inter,system-ui,Arial;background:#061026;color:#fff}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px}
        .card{max-width:900px;width:100%;text-align:center}
        h1{font-size:2.8rem;margin:0 0 12px}
        p{color:#cbd5e1;margin:0 0 20px}
        .nav{display:flex;gap:12px;align-items:center;justify-content:center}
        .arrow{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:999px;border:1px solid rgba(255,255,255,.08);background:transparent;color:#fff;text-decoration:none}
        .start{display:inline-block;padding:14px 20px;border-radius:12px;background:#38bdf8;color:#02111a;font-weight:800;text-decoration:none;margin-top:18px}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Mulai Latihanmu Sekarang</h1>
            <p>Bergabung sebagai member untuk akses penuh, atau lakukan sign in cepat sebagai daily pass untuk mulai latihan hari ini.</p>
            <div class="nav">
                <a class="arrow" href="{{ route('landing.step2') }}">←</a>
                <a class="start" href="{{ route('login') }}">Mulai Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>
