<?php

use App\Helpers\RouteHelpers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
| Menangani seluruh alur login (multi-role) dan logout.
*/

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', fn () => RouteHelpers::redirectByRole());

// ── Login page ────────────────────────────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login', [
        'pageTitle' => 'Login Portal Arena Gym',
        'roles'     => [
            [
                'value'       => 'admin',
                'label'       => 'Admin',
                'description' => 'Kelola member, check-in, dan operasional.',
                'icon'        => 'shield-check',
                'img'         => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'value'       => 'master_admin',
                'label'       => 'Master Admin',
                'description' => 'Akses penuh seluruh modul sistem.',
                'icon'        => 'key',
                'img'         => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'value'       => 'cashier',
                'label'       => 'Kasir',
                'description' => 'Kelola transaksi dan pembayaran harian.',
                'icon'        => 'wallet',
                'img'         => 'https://images.unsplash.com/photo-1556742044-3c52d6e88c62?auto=format&fit=crop&q=80&w=400',
            ],
        ],
    ]);
})->name('login');

// ── Role-specific login shortcuts (redirect ke /login dengan ?role=xxx) ───────
Route::get('/login/admin',        fn () => redirect()->route('login', ['role' => 'admin']))->name('admin.login');
Route::post('/login/admin',       fn () => redirect()->route('login', ['role' => 'admin']))->name('admin.login.submit');

Route::get('/login/cashier',      fn () => redirect()->route('login', ['role' => 'cashier']))->name('cashier.login');
Route::post('/login/cashier',     fn () => redirect()->route('login', ['role' => 'cashier']))->name('cashier.login.submit');

Route::get('/login/master-admin', fn () => redirect()->route('login', ['role' => 'master_admin']))->name('master-admin.login');
Route::post('/login/master-admin',fn () => redirect()->route('login', ['role' => 'master_admin']))->name('master-admin.login.submit');

// ── Login submit ──────────────────────────────────────────────────────────────
Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'role'     => ['required', 'in:admin,master_admin,cashier'],
        'login'    => ['required'],
        'password' => ['required'],
    ]);

    // Coba cari user sesuai role yang dipilih di form
    $user = User::query()
        ->where('login', $validated['login'])
        ->where('role', $validated['role'])
        ->first();

    // Fallback: cari tanpa filter role (agar cashier/admin tetap bisa login dari /login)
    if (! $user) {
        $user = User::query()->where('login', $validated['login'])->first();
    }

    if (
        ! $user
        || ! in_array($user->role, ['admin', 'master_admin', 'cashier'], true)
        || ! Hash::check($validated['password'], $user->password)
    ) {
        return back()
            ->withErrors(['login' => 'Username atau password tidak sesuai.'])
            ->withInput();
    }

    $request->session()->regenerate();
    $request->session()->put('auth', [
        'role'    => $user->role,
        'login'   => $user->login,
        'user_id' => $user->id,
        'name'    => $user->name,
    ]);

    return in_array($user->role, ['admin', 'master_admin'], true)
        ? redirect()->route('admin.dashboard')
        : redirect()->route('cashier.dashboard');
})->name('login.submit');

// ── Logout ────────────────────────────────────────────────────────────────────
Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
