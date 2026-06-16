<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Reset Password | Arena Fitness</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght@400;500&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet"/>
    <style>
        .metal-finish { background: linear-gradient(135deg, #222 0%, #111 100%); }
        .auth-container { border: 1px solid rgba(255, 255, 255, 0.1); }
        .bg-background { background-color: #131313; }
        .bg-primary-container { background-color: #ff5540; }
        .border-surface-variant { border-color: #353535; }
        .text-primary { color: #ffb4a8; }
        .text-on-background { color: #e2e2e2; }
        .text-on-primary-container { color: #5c0000; }
        .text-on-surface-variant { color: #ebbbb4; }
        .font-body-md { font-family: 'Hanken Grotesk', sans-serif; }
        .font-label-caps { font-family: 'JetBrains Mono', monospace; }
        .font-headline-md, .font-headline-lg { font-family: 'Oswald', sans-serif; }
    </style>
</head>
<body class="bg-background text-on-background font-body-md">
<div class="relative min-h-screen flex items-center justify-center px-5 overflow-hidden">
    <div class="absolute inset-0 bg-black/70"></div>
    <main class="relative z-10 w-full max-w-[520px] auth-container metal-finish p-8 md:p-12 shadow-2xl">
        <div class="flex justify-center mb-10">
            <a href="{{ route('login') }}" class="text-3xl font-headline-md font-bold text-primary tracking-tighter uppercase">Arena Fitness</a>
        </div>
        <div class="text-center mb-10">
            <h1 class="font-headline-lg text-4xl md:text-5xl uppercase">Reset Password</h1>
            <p class="text-on-surface-variant mt-4">Buat password baru untuk akun member Anda.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/40 text-red-300 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label class="font-label-caps text-sm text-on-surface-variant/70 block mb-2" for="email">EMAIL MEMBER</label>
                <input class="w-full bg-transparent border-b-2 border-surface-variant focus:border-primary-container outline-none py-3 font-label-caps text-on-background" id="email" name="email" type="email" value="{{ old('email', $email) }}" required/>
            </div>
            <div>
                <label class="font-label-caps text-sm text-on-surface-variant/70 block mb-2" for="password">PASSWORD BARU</label>
                <input class="w-full bg-transparent border-b-2 border-surface-variant focus:border-primary-container outline-none py-3 font-label-caps text-on-background" id="password" name="password" type="password" required/>
            </div>
            <div>
                <label class="font-label-caps text-sm text-on-surface-variant/70 block mb-2" for="password_confirmation">KONFIRMASI PASSWORD</label>
                <input class="w-full bg-transparent border-b-2 border-surface-variant focus:border-primary-container outline-none py-3 font-label-caps text-on-background" id="password_confirmation" name="password_confirmation" type="password" required/>
            </div>
            <button class="w-full bg-primary-container text-on-primary-container py-5 font-headline-md text-3xl uppercase tracking-widest transition-all active:scale-95" type="submit">Simpan Password</button>
        </form>
    </main>
</div>
</body>
</html>
