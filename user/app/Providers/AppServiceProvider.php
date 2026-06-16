<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. Tambahkan baris ini di atas

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 2. Tambahkan kode ini agar URL Form selalu HTTPS
        if (str_contains(env('APP_URL'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}