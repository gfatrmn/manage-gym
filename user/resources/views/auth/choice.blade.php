<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Akses</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#020617; color:#f8fafc; font-family:Inter, Arial, sans-serif; }
        .box { width:min(520px,100%); padding:32px; border-radius:24px; background:rgba(15, 23, 42, .92); box-shadow:0 24px 80px rgba(0,0,0,.35); border:1px solid rgba(148,163,184,.08); }
        h1 { margin:0 0 10px; font-size:32px; }
        p { margin:0 0 28px; color:#94a3b8; }
        .actions { display:grid; gap:14px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:16px 20px; border-radius:14px; border:0; font-size:16px; font-weight:700; cursor:pointer; text-decoration:none; }
        .btn-login { background:#38bdf8; color:#020617; }
        .btn-signin { background:#0f172a; color:#f8fafc; border:1px solid rgba(56,189,248,.32); }
    </style>
</head>
<body>
    <main class="box">
        <h1>Pilih Akses</h1>
        <p>Login untuk member yang sudah didaftarkan kasir. Pendaftaran dan perpanjangan membership dilakukan lewat kasir.</p>
        <div class="actions">
            <a class="btn btn-login" href="{{ route('login') }}">Login</a>
            <span class="btn btn-signin">Daftar via Kasir</span>
        </div>
    </main>
</body>
</html>
