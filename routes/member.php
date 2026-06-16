<?php

use App\Models\Announcement;
use App\Models\GymMember;
use App\Models\MemberFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

$isLegacyMemberActive = function (?User $user): bool {
    $member = $user?->gymMember;

    return (bool) ($member?->expires_at && $member->expires_at->copy()->startOfDay()->gte(now()->startOfDay()));
};

$legacyInactiveRedirect = function () {
    return redirect()
        ->route('member.login')
        ->withErrors(['email' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.']);
};

$legacyActiveMemberSession = function (Request $request) use ($isLegacyMemberActive, $legacyInactiveRedirect): array {
    if (! session('auth.role') || session('auth.role') !== 'member') {
        return ['redirect' => redirect()->route('member.login')];
    }

    $user = User::query()->where('id', session('auth.id'))->where('role', 'member')->first();
    if (! $user) {
        $request->session()->forget('auth');
        return ['redirect' => redirect()->route('member.login')];
    }

    if (! $isLegacyMemberActive($user)) {
        $request->session()->forget('auth');
        return ['redirect' => $legacyInactiveRedirect()];
    }

    return ['user' => $user, 'member' => $user->gymMember];
};

// Member Login
Route::get('/member/login', function () {
    return view('member.login');
})->name('member.login');

Route::post('/member/login', function (Request $request) use ($isLegacyMemberActive, $legacyInactiveRedirect) {
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::query()
        ->where('email', $validated['email'])
        ->where('role', 'member')
        ->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->withInput();
    }

    if (! $isLegacyMemberActive($user)) {
        return $legacyInactiveRedirect()->withInput($request->only('email'));
    }

    $request->session()->regenerate();
    $request->session()->put('auth', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'member',
    ]);

    return redirect()->route('member.dashboard');
})->name('member.login.submit');

Route::get('/member/activate', function (Request $request) {
    $memberSearch = trim((string) $request->query('q', ''));

    $activationMembers = GymMember::query()
        ->with('user')
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

    return view('member.activate', [
        'activationMembers' => $activationMembers,
        'memberSearch' => $memberSearch,
    ]);
})->name('member.activate.show');

Route::post('/member/activate', function (Request $request) {
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

    User::query()->where('id', $memberUser->id)->update([
        'password' => Hash::make($validated['password']),
        'updated_at' => now(),
    ]);

    return redirect()->route('member.login')->with('status', 'Password berhasil dibuat. Silakan login.');
})->name('member.activate.store');

// Member Dashboard
Route::get('/member/dashboard', function (Request $request) use ($legacyActiveMemberSession) {
    $session = $legacyActiveMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
    
    $announcements = collect();
    if ($member) {
        $announcements = Announcement::query()
            ->where('status', 'active')
            ->where('body', 'like', "[TARGET_MEMBER_ID:{$member->id}]%")
            ->latest('publish_at')
            ->get();
    }

    return view('member.dashboard', [
        'user' => $user,
        'member' => $member,
        'totalCheckins' => $totalCheckins,
        'announcements' => $announcements,
    ]);
})->name('member.dashboard');

// Member History
Route::get('/member/history', function (Request $request) use ($legacyActiveMemberSession) {
    $session = $legacyActiveMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    return view('member.history', ['user' => $user]);
})->name('member.history');

Route::get('/member/statistics', function (Request $request) use ($legacyActiveMemberSession) {
    $session = $legacyActiveMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $checkins = $member ? $member->verifiedCheckins()->get() : collect();
    $totalCheckins = $checkins->count();
    $thisMonthCheckins = $checkins->filter(fn ($item) => $item->checked_in_at?->isSameMonth(now()))->count();
    $lastCheckinAt = $checkins->first()?->checked_in_at;

    return view('member.statistics', [
        'user' => $user,
        'member' => $member,
        'totalCheckins' => $totalCheckins,
        'thisMonthCheckins' => $thisMonthCheckins,
        'lastCheckinAt' => $lastCheckinAt,
    ]);
})->name('member.statistics');

// Logout
Route::post('/member/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('member.login');
})->name('member.logout');

Route::post('/member/feedback', function (Request $request) use ($legacyActiveMemberSession) {
    $session = $legacyActiveMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $validated = $request->validate([
        'subject' => ['required', 'string', 'max:120'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    $user = $session['user'];
    $member = $session['member'];

    MemberFeedback::query()->create([
        'user_id' => $user?->id,
        'gym_member_id' => $member?->id,
        'name' => $user?->name ?? ($member?->full_name ?? 'Member'),
        'email' => $user?->email,
        'subject' => $validated['subject'],
        'message' => $validated['message'],
    ]);

    return back()->with('status', 'Terima kasih. Kritik dan saran kamu sudah terkirim.');
})->name('member.feedback.store');
