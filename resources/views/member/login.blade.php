<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>IRON ELITE | Login Member</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght=400;600;700&family=JetBrains+Mono:wght=400;500;700&family=Hanken+Grotesk:wght=400;600&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-dim": "#131313",
                        "on-surface-variant": "#ebbbb4",
                        "on-surface": "#e2e2e2",
                        "background": "#131313",
                        "primary": "#ffb4a8",
                        "surface-container": "#1f1f1f",
                        "surface-container-highest": "#353535",
                        "on-background": "#e2e2e2",
                        "surface-container-lowest": "#0e0e0e",
                        "surface-variant": "#353535",
                        "brand-red": "#FF0000"
                    },
                    fontFamily: {
                        "label-caps": ["JetBrains Mono"],
                        "body-md": ["Hanken Grotesk"],
                        "headline-lg": ["Oswald"]
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style>
        body {
            font-family: 'Hanken Grotesk', sans-serif;
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 bg-surface-container border border-surface-variant rounded-xl">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="width: 140px; height: auto; max-width: 100%;">
        </div>
        <h2 class="font-label-caps text-sm uppercase text-on-surface-variant mb-8 text-center">Login Member</h2>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-900/50 border border-red-500 text-red-200 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('status'))
            <div class="mb-4 p-4 bg-green-900/40 border border-green-500 text-green-100 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.login.submit') }}">
            @csrf
            <div class="mb-6">
                <label class="block font-label-caps text-xs uppercase text-on-surface-variant mb-2">Email</label>
                <input type="email" name="email" required class="w-full bg-surface-container-highest border border-surface-variant rounded-lg p-3 text-on-surface focus:border-brand-red focus:outline-none" placeholder="email@example.com">
            </div>
            <div class="mb-8">
                <label class="block font-label-caps text-xs uppercase text-on-surface-variant mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-surface-container-highest border border-surface-variant rounded-lg p-3 text-on-surface focus:border-brand-red focus:outline-none" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-brand-red text-black font-label-caps uppercase py-3 rounded-lg hover:bg-white transition-colors duration-300">
                Login
            </button>
        </form>
        <div class="mt-5 text-center">
            <a href="{{ route('member.activate.show') }}" class="font-label-caps text-xs uppercase text-on-surface-variant hover:text-primary">
                Aktivasi akun member
            </a>
        </div>
    </div>
</body>
</html>
