<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function showLogin(Request $request): Response|RedirectResponse
    {
        $authRole = $request->session()->get('auth.role');
        if ($authRole) {
            return RouteHelpers::redirectByRole();
        }

        return Inertia::render('Auth/Login', [
            'pageTitle' => 'Login Arena Gym',
        ]);
    }

    public function submitLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['nullable', 'in:member,admin,master_admin,cashier'],
            'login' => ['required_without:email', 'nullable', 'string'],
            'email' => ['required_without:login', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim((string) ($validated['login'] ?? $validated['email'] ?? ''));

        // Query user by username or email
        $userQuery = User::query()
            ->with('gymMember')
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('login', $identifier);
            });

        // If specific role requested (e.g. from tests), filter by it; otherwise search all
        if (! empty($validated['role'])) {
            $user = (clone $userQuery)->where('role', $validated['role'])->first();
        } else {
            $user = $userQuery->first();
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'Username/Email atau password tidak sesuai.',
                    'email' => 'Username/Email atau password tidak sesuai.',
                ])
                ->withInput();
        }

        if ($user->role === 'member') {
            if (! EnsureRole::isMemberMembershipActive($user->gymMember)) {
                return back()
                    ->withErrors([
                        'login' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.',
                        'email' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.',
                    ])
                    ->withInput();
            }

            $request->session()->regenerate();
            $request->session()->put('auth', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'member',
            ]);
            $request->session()->put('show_whatsapp_channel_prompt', true);

            return redirect()->route('member.dashboard');
        }

        $request->session()->regenerate();
        $request->session()->put('auth', [
            'id' => $user->id,
            'role' => $user->role,
            'login' => $user->login,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return in_array($user->role, ['admin', 'master_admin'], true)
            ? redirect()->route('admin.dashboard')
            : redirect()->route('cashier.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function root(): RedirectResponse
    {
        return RouteHelpers::redirectByRole();
    }
}
