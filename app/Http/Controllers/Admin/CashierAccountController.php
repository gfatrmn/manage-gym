<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CashierAccountController extends Controller
{
    public function index(): Response
    {
        $cashiers = User::query()
            ->where('role', 'cashier')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'login' => $user->login,
                'created_at' => $user->created_at?->translatedFormat('d M Y') ?? '-',
            ]);

        return Inertia::render('Admin/CashierAccounts', [
            'pageTitle' => 'Akun Kasir - Arena Gym',
            'activePage' => 'cashier-accounts',
            'cashiers' => $cashiers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
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

        return redirect()
            ->route('admin.cashier-accounts')
            ->with('status', "Akun kasir berhasil ditambahkan dengan username: {$login}");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== 'cashier') {
            abort(404);
        }

        $user->delete();

        return redirect()->route('admin.cashier-accounts')->with('status', 'Akun kasir berhasil dihapus.');
    }
}

