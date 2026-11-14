<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureRole;
use App\Models\GymMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MemberAuthController extends Controller
{
    public function showLogin(Request $request): Response|RedirectResponse
    {
        return redirect()->route('login');
    }

    public function submitLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->with('gymMember')
            ->where(function ($query) use ($validated) {
                $query->where('email', $validated['email'])
                    ->orWhere('login', $validated['email']);
            })
            ->where('role', 'member')
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()
                ->withErrors([
                    'email' => 'Email/Username atau password tidak sesuai.',
                    'login' => 'Email/Username atau password tidak sesuai.',
                ])
                ->withInput();
        }

        if (! EnsureRole::isMemberMembershipActive($user->gymMember)) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.',
                    'login' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.',
                ])
                ->withInput($request->only('email', 'login'));
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

    public function showActivate(Request $request): Response
    {
        $memberSearch = trim((string) $request->query('q', ''));

        $activationMembers = GymMember::query()
            ->with('user:id,name,email,login')
            ->where('status', 'member')
            ->when($memberSearch !== '', function ($query) use ($memberSearch) {
                $query->where(function ($q) use ($memberSearch) {
                    $q->where('full_name', 'like', '%' . $memberSearch . '%')
                        ->orWhere('email', 'like', '%' . $memberSearch . '%')
                        ->orWhere('phone', 'like', '%' . $memberSearch . '%')
                        ->orWhere('checkin_code', 'like', '%' . $memberSearch . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Member/Activate', [
            'activationMembers' => $activationMembers,
            'memberSearch' => $memberSearch,
        ]);
    }

    public function storeActivate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $memberUser = User::query()
            ->where('email', $validated['email'])
            ->where('role', 'member')
            ->first();

        if (! $memberUser) {
            return back()
                ->withErrors(['email' => 'Email member tidak ditemukan.'])
                ->withInput();
        }

        $memberUser->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('member.login')
            ->with('status', 'Password berhasil dibuat. Silakan login.');
    }

    public function showForgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('role', 'member')
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Email member tidak ditemukan.'])
                ->withInput();
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);

        Mail::raw("Gunakan link berikut untuk reset password Arena Fitness:\n\n{$resetLink}\n\nLink berlaku 60 menit.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Password Arena Fitness');
        });

        return back()
            ->with('status', 'Link reset password sudah dibuat.')
            ->with('reset_link', app()->environment('local') ? $resetLink : null);
    }

    public function showResetPassword(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $record || ! Hash::check($validated['token'], $record->token)) {
            return back()
                ->withErrors(['email' => 'Token reset password tidak valid.'])
                ->withInput();
        }

        if ($record->created_at && now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()
                ->withErrors(['email' => 'Token reset password sudah kedaluwarsa.'])
                ->withInput();
        }

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('role', 'member')
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Email member tidak ditemukan.'])
                ->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()
            ->route('member.login')
            ->with('status', 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }
}

