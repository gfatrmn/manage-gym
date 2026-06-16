<?php

use App\Helpers\RouteHelpers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/cashier-accounts', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $cashiers = User::query()
        ->where('role', 'cashier')
        ->orderBy('name')
        ->get();

    return view('admin.cashier_accounts', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'pageTitle' => 'Akun Kasir - Arena Gym',
        'activePage' => 'cashier-accounts',
        'cashiers' => $cashiers,
    ]));
})->name('cashier-accounts');

Route::post('/cashier-accounts', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    $baseLogin = Str::of($validated['login'])
        ->lower()
        ->replaceMatches('/\s+/', '')
        ->replaceMatches('/[^a-z0-9._-]/', '')
        ->value();
    $baseLogin = $baseLogin !== '' ? $baseLogin : 'kasir';

    $login = $baseLogin;
    $counter = 2;
    while (User::query()->where('login', $login)->exists()) {
        $login = $baseLogin . $counter;
        $counter++;
    }

    User::query()->create([
        'name' => $validated['name'],
        'login' => $login,
        'email' => null,
        'role' => 'cashier',
        'password' => Hash::make($validated['password']),
    ]);

    return redirect()->route('admin.cashier-accounts')->with('status', "Akun kasir berhasil ditambahkan dengan username: {$login}");
})->name('cashier-accounts.store');

Route::delete('/cashier-accounts/{user}', function (User $user) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    if ($user->role !== 'cashier') abort(404);
    $user->delete();
    return redirect()->route('admin.cashier-accounts')->with('status', 'Akun kasir berhasil dihapus.');
})->name('cashier-accounts.destroy');
