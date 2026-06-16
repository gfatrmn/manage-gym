<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Berhasil</title>
    <style>
        body { font-family: Arial, sans-serif; background:#0f172a; color:#f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card { width:min(520px,100%); background:#1e293b; padding:28px; border-radius:20px; box-shadow:0 18px 50px rgba(15,23,42,.35); }
        h1 { margin:0 0 8px; font-size:24px; }
        p { margin:0 0 16px; color:#94a3b8; }
        .info { background:#0f172a; border:1px solid #334155; border-radius:14px; padding:18px; margin:18px 0; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#38bdf8; color:#020617; border:none; border-radius:12px; padding:12px 18px; font-weight:700; text-decoration:none; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Sign In Berhasil</h1>
        <p>Pengunjung daily pass sudah tercatat.</p>
        <div class="info">
            <strong>Nama:</strong> {{ $member->full_name }}<br>
            <strong>Telepon:</strong> {{ $member->phone }}<br>
            <strong>Email:</strong> {{ $member->email ?? '-' }}<br>
            <strong>Kode Check-in:</strong> {{ $member->checkin_code }}
        </div>
        <a class="btn" href="{{ route('login') }}">Kembali ke Login</a>
    </main>
</body>
</html>
