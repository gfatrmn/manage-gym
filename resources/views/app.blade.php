<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Arena Gym') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|space-grotesk:500,700" rel="stylesheet" />
        <script>
            (() => {
                let saved = localStorage.getItem('arena-gym-theme');
                if (!localStorage.getItem('arena-theme-default-v2')) {
                    localStorage.setItem('arena-theme-default-v2', '1');
                    localStorage.setItem('arena-gym-theme', 'light');
                    saved = 'light';
                }
                const isDark = saved === 'dark';
                document.documentElement.classList.toggle('dark', isDark);
                document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            })();
        </script>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
        @inertiaHead
    </head>
    <body class="antialiased selection:bg-rose-500/30 selection:text-rose-900 dark:selection:text-rose-200">
        @inertia
    </body>
</html>

