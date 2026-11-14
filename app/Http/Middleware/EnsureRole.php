<?php

namespace App\Http\Middleware;

use App\Models\GymMember;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $role = $request->session()->get('auth.role');

        if ($scope === 'admin') {
            if (! in_array($role, ['admin', 'master_admin'], true)) {
                return redirect()->route('admin.login');
            }

            return $next($request);
        }

        if ($scope === 'cashier') {
            if (! in_array($role, ['cashier', 'master_admin'], true)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }

                return redirect()->route('cashier.login');
            }

            return $next($request);
        }

        if ($scope === 'member') {
            if ($role !== 'member') {
                return redirect()->route('member.login');
            }

            $user = User::query()
                ->with('gymMember')
                ->where('id', $request->session()->get('auth.id'))
                ->where('role', 'member')
                ->first();

            if (! $user) {
                $request->session()->forget(['auth', 'show_whatsapp_channel_prompt']);

                return redirect()
                    ->route('member.login')
                    ->withErrors(['email' => 'Sesi login berakhir, silakan login kembali.']);
            }

            if (! self::isMemberMembershipActive($user->gymMember)) {
                $request->session()->forget(['auth', 'show_whatsapp_channel_prompt']);

                return redirect()
                    ->route('member.login')
                    ->withErrors(['email' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.']);
            }

            $request->attributes->set('memberUser', $user);
            $request->attributes->set('gymMember', $user->gymMember);

            return $next($request);
        }

        return $next($request);
    }

    public static function isMemberMembershipActive(?GymMember $member): bool
    {
        return (bool) ($member?->expires_at && $member->expires_at->copy()->startOfDay()->gte(now()->startOfDay()));
    }
}

