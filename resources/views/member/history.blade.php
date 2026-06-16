<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>IRON ELITE | Riwayat Member</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght=400;500;700&family=Hanken+Grotesk:wght=400;600&display=swap" rel="stylesheet"/>
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
</head>
<body class="bg-background text-on-background min-h-screen">
    <header class="w-full bg-background border-b border-surface-variant py-6">
        <div class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop flex justify-between items-center">
            <a href="{{ route('member.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="width: 110px; height: auto;">
            </a>
            <form method="POST" action="{{ route('member.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="bg-brand-red text-black font-label-caps px-4 py-2 rounded uppercase text-sm">Logout</button>
            </form>
        </div>
    </header>
    <main class="max-w-screen-2xl mx-auto px-margin-mobile md:px-margin-desktop py-12">
        <h1 class="font-headline-lg text-3xl uppercase text-primary mb-8">Riwayat Kunjungan</h1>
        <div class="bg-surface-container border border-surface-variant rounded-xl p-6">
            <p class="text-on-surface-variant">Riwayat kunjungan akan ditampilkan di sini.</p>
        </div>
    </main>
</body>
</html>
