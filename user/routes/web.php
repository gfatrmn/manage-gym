<?php

use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\Announcement;
use App\Models\MemberFeedback;
use App\Models\ProfilePhotoChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

$isMemberMembershipActive = function (?GymMember $member): bool {
    return (bool) ($member?->expires_at && $member->expires_at->copy()->startOfDay()->gte(now()->startOfDay()));
};

$inactiveMembershipRedirect = function () {
    return redirect()
        ->route('login')
        ->withErrors(['login' => 'Masa aktif membership Anda sudah habis. Silakan perpanjang di kasir agar akun bisa dibuka kembali.']);
};

$activeMemberSession = function (Request $request) use ($isMemberMembershipActive, $inactiveMembershipRedirect): array {
    if (! session('auth.role') || session('auth.role') !== 'member') {
        return ['redirect' => redirect()->route('login')];
    }

    $user = User::query()->where('id', session('auth.id'))->where('role', 'member')->first();
    if (! $user) {
        $request->session()->forget(['auth', 'show_whatsapp_channel_prompt']);
        return ['redirect' => redirect()->route('login')->withErrors(['email' => 'Sesi login berakhir, silakan login kembali.'])];
    }

    $member = $user->gymMember;
    if (! $isMemberMembershipActive($member)) {
        $request->session()->forget(['auth', 'show_whatsapp_channel_prompt']);
        return ['redirect' => $inactiveMembershipRedirect()];
    }

    return ['user' => $user, 'member' => $member];
};

$memberNotificationData = function (?User $user, ?GymMember $member, bool $pullChannelPrompt = false): array {
    $announcements = collect();
    if ($member) {
        $announcements = Announcement::query()
            ->where('status', 'active')
            ->where('body', 'like', "[TARGET_MEMBER_ID:{$member->id}]%")
            ->latest('publish_at')
            ->get();
    }

    $membershipWarning = null;
    if ($member?->expires_at) {
        $daysLeft = now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false);

        if ($daysLeft >= 0 && $daysLeft <= 7) {
            $membershipWarning = [
                'days_left' => (int) $daysLeft,
                'title' => $daysLeft <= 3 ? 'Peringatan Membership Mendesak' : 'Peringatan Membership',
                'message' => $daysLeft === 0
                    ? 'Masa aktif membership Anda berakhir hari ini. Silakan hubungi kasir untuk perpanjangan.'
                    : "Masa aktif membership Anda tersisa {$daysLeft} hari. Silakan hubungi kasir untuk perpanjangan.",
                'level' => $daysLeft <= 3 ? 'urgent' : 'warning',
            ];
        }
    }

    $channelUrl = config('services.whatsapp.channel_url');
    $showWhatsAppChannelPrompt = $pullChannelPrompt
        ? (bool) session()->pull('show_whatsapp_channel_prompt', false)
        : (bool) session('show_whatsapp_channel_prompt', false);

    return [
        'memberAnnouncements' => $announcements,
        'announcements' => $announcements,
        'membershipWarning' => $membershipWarning,
        'channelUrl' => $channelUrl,
        'showWhatsAppChannelPrompt' => $showWhatsAppChannelPrompt,
        'hasMembershipWarning' => filled($membershipWarning),
        'shouldShowChannelPrompt' => $showWhatsAppChannelPrompt && filled($channelUrl),
    ];
};

View::composer('member.*', function ($view) use ($memberNotificationData) {
    if (! session('auth.role') || session('auth.role') !== 'member') {
        return;
    }

    $user = User::query()->where('id', session('auth.id'))->where('role', 'member')->first();
    $member = $user?->gymMember;
    $view->with($memberNotificationData($user, $member, true));
});

Route::get('/', function () {
    $authId = session('auth.id');
    $authRole = session('auth.role');
    if ($authId && $authRole === 'member') {
        $existingUser = User::query()->where('id', $authId)->where('role', 'member')->first();
        if ($existingUser) {
            return redirect()->route('dashboard');
        }
        session()->forget('auth');
    }

    return redirect()->route('login')->with('status', 'Pendaftaran dan perpanjangan member hanya melalui kasir.');
});

Route::get('/landing/1', function () {
    return redirect()->route('login')->with('status', 'Pendaftaran dan perpanjangan member hanya melalui kasir.');
})->name('landing.step1');

Route::get('/landing/2', function () {
    return redirect()->route('login')->with('status', 'Pendaftaran dan perpanjangan member hanya melalui kasir.');
})->name('landing.step2');

Route::get('/landing/3', function () {
    return redirect()->route('login')->with('status', 'Pendaftaran dan perpanjangan member hanya melalui kasir.');
})->name('landing.step3');

Route::get('/login', function () {
    $authId = session('auth.id');
    $authRole = session('auth.role');
    if ($authId && $authRole === 'member') {
        $existingUser = User::query()->where('id', $authId)->where('role', 'member')->first();
        if ($existingUser) {
            return redirect()->route('dashboard');
        }
        session()->forget('auth');
    }

    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) use ($isMemberMembershipActive, $inactiveMembershipRedirect) {
    $authId = session('auth.id');
    $authRole = session('auth.role');
    if ($authId && $authRole === 'member') {
        $existingUser = User::query()->where('id', $authId)->where('role', 'member')->first();
        if ($existingUser) {
            return redirect()->route('dashboard');
        }
        $request->session()->forget('auth');
    }

    $validated = $request->validate([
        'login' => ['required', 'string', 'max:255'],
        'password' => ['required'],
    ]);

    $user = User::query()
        ->where('login', $validated['login'])
        ->where('role', 'member')
        ->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return back()
            ->withErrors(['login' => 'Username atau password tidak sesuai.'])
            ->withInput();
    }

    if (! $isMemberMembershipActive($user->gymMember)) {
        return $inactiveMembershipRedirect()->withInput($request->only('login'));
    }

    $request->session()->regenerate();
    $request->session()->put('auth', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'member',
    ]);
    $request->session()->put('show_whatsapp_channel_prompt', true);

    return redirect()->route('dashboard');
})->name('login.submit');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
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
})->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, string $token) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
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
        ->route('login')
        ->with('status', 'Password berhasil diperbarui. Silakan login dengan password baru.');
})->name('password.update');

// Member registration and renewal are only via cashier
Route::get('/signup', function () {
    return redirect()->route('login')->with('status', 'Pendaftaran member hanya melalui kasir.');
})->name('signup');

Route::post('/signup', function (Request $request) {
    return redirect()->route('login')->with('status', 'Pendaftaran member hanya melalui kasir.');
})->name('signup.submit');

Route::get('/dashboard', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
    $membershipStatus = collect([
        $member?->member_status,
        $member?->membership_plan,
        $member?->status,
    ])->first(fn ($value) => filled($value)) ?? 'member';

    $announcements = collect();
    if ($member) {
        $announcements = Announcement::query()
            ->where('status', 'active')
            ->where('body', 'like', "[TARGET_MEMBER_ID:{$member->id}]%")
            ->latest('publish_at')
            ->get();
    }

    $membershipWarning = null;
    if ($member?->expires_at) {
        $daysLeft = now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false);

        if ($daysLeft >= 0 && $daysLeft <= 7) {
            $membershipWarning = [
                'days_left' => (int) $daysLeft,
                'title' => $daysLeft <= 3 ? 'Peringatan Membership Mendesak' : 'Peringatan Membership',
                'message' => $daysLeft === 0
                    ? 'Masa aktif membership Anda berakhir hari ini. Silakan hubungi kasir untuk perpanjangan.'
                    : "Masa aktif membership Anda tersisa {$daysLeft} hari. Silakan hubungi kasir untuk perpanjangan.",
                'level' => $daysLeft <= 3 ? 'urgent' : 'warning',
            ];
        }
    }

    return view('member.dashboard', [
        'user' => $user,
        'member' => $member,
        'totalCheckins' => $totalCheckins,
        'membershipStatus' => $membershipStatus,
        'announcements' => $announcements,
        'membershipWarning' => $membershipWarning,
    ]);
})->name('dashboard');

Route::get('/statistics', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];

    $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
    $thisMonthCheckins = $member
        ? $member->verifiedCheckins()->whereMonth('checked_in_at', now()->month)->whereYear('checked_in_at', now()->year)->count()
        : 0;
    $lastCheckin = $member ? $member->verifiedCheckins()->latest('checked_in_at')->first()?->checked_in_at : null;
    $weekly = collect(range(6, 0))->map(function ($offset) use ($member) {
        $date = now()->copy()->subDays($offset);
        $count = $member
            ? $member->verifiedCheckins()
                ->whereDate('checked_in_at', $date->toDateString())
                ->count()
            : 0;
        return [
            'label' => $date->translatedFormat('D'),
            'value' => $count,
        ];
    });
    $monthly = collect(range(5, 0))->map(function ($offset) use ($member) {
        $date = now()->copy()->subMonths($offset);
        $count = $member
            ? $member->verifiedCheckins()
                ->whereYear('checked_in_at', $date->year)
                ->whereMonth('checked_in_at', $date->month)
                ->count()
            : 0;
        return [
            'label' => $date->translatedFormat('M Y'),
            'value' => $count,
        ];
    });

    return view('member.statistics', [
        'user' => $user,
        'member' => $member,
        'totalCheckins' => $totalCheckins,
        'thisMonthCheckins' => $thisMonthCheckins,
        'lastCheckin' => $lastCheckin,
        'weeklyStats' => $weekly,
        'monthlyStats' => $monthly,
    ]);
})->name('statistics');

Route::get('/messages', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $announcements = collect();
    if ($member) {
        $announcements = Announcement::query()
            ->where('status', 'active')
            ->where('body', 'like', "[TARGET_MEMBER_ID:{$member->id}]%")
            ->latest('publish_at')
            ->get();
    }

    return view('member.messages', [
        'user' => $user,
        'member' => $member,
        'membershipStatus' => $member?->status ?? 'member',
        'announcements' => $announcements,
    ]);
})->name('messages');

Route::get('/history', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $checkins = $member
        ? $member->verifiedCheckins()
            ->latest('checked_in_at')
            ->paginate(10)
        : collect();

    $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
    $thisMonthCheckins = $member
        ? $member->verifiedCheckins()
            ->whereMonth('checked_in_at', now()->month)
            ->whereYear('checked_in_at', now()->year)
            ->count()
        : 0;
    $thisWeekCheckins = $member
        ? $member->verifiedCheckins()
            ->whereBetween('checked_in_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count()
        : 0;

    $chartStart = $member?->joined_at ? $member->joined_at->copy()->startOfDay() : now()->copy()->subDays(29)->startOfDay();
    $chartEnd = now()->copy()->startOfDay();
    if ($chartStart->diffInDays($chartEnd) > 30) {
        $chartStart = $chartEnd->copy()->subDays(29);
    }

    $chartCheckins = $member
        ? $member->verifiedCheckins()
            ->whereBetween('checked_in_at', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
            ->get()
            ->groupBy(fn ($checkin) => $checkin->checked_in_at?->format('Y-m-d'))
        : collect();

    $trainingChartLabels = [];
    $trainingChartValues = [];
    for ($date = $chartStart->copy(); $date->lte($chartEnd); $date->addDay()) {
        $key = $date->format('Y-m-d');
        $trainingChartLabels[] = $date->translatedFormat('d M');
        $trainingChartValues[] = $chartCheckins->get($key, collect())->count();
    }

    $trackedDays = max(count($trainingChartValues), 1);
    $activeTrainingDays = collect($trainingChartValues)->filter(fn ($count) => $count > 0)->count();
    $trainingConsistency = (int) round(($activeTrainingDays / $trackedDays) * 100);
    $trainingAssessment = $trainingConsistency >= 50
        ? 'Rajin'
        : ($trainingConsistency >= 25 ? 'Cukup Konsisten' : 'Perlu Lebih Konsisten');

    return view('member.history', [
        'user' => $user,
        'checkins' => $checkins,
        'totalCheckins' => $totalCheckins,
        'thisMonthCheckins' => $thisMonthCheckins,
        'thisWeekCheckins' => $thisWeekCheckins,
        'trainingChartLabels' => $trainingChartLabels,
        'trainingChartValues' => $trainingChartValues,
        'trainingConsistency' => $trainingConsistency,
        'trainingAssessment' => $trainingAssessment,
        'activeTrainingDays' => $activeTrainingDays,
        'trackedTrainingDays' => $trackedDays,
    ]);
})->name('history');

Route::get('/barcode', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];

    $membershipStatus = collect([
        $member?->member_status,
        $member?->membership_plan,
        $member?->status,
    ])->first(fn ($value) => filled($value)) ?? 'member';

    return view('member.barcode', [
        'user' => $user,
        'member' => $member,
        'checkinCode' => $member?->checkin_code,
        'membershipStatus' => $membershipStatus,
        'sessionId' => 'ARENA-' . random_int(100000, 999999),
    ]);
})->name('barcode');

Route::get('/membership', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $membershipStatus = collect([
        $member?->member_status,
        $member?->membership_plan,
        $member?->status,
    ])->first(fn ($value) => filled($value)) ?? 'member';

    $joinedAt = $member?->joined_at;
    $expiresAt = $member?->expires_at;
    $remainingDays = $expiresAt ? max(0, now()->startOfDay()->diffInDays($expiresAt, false)) : null;
    $totalDays = ($joinedAt && $expiresAt)
        ? max(1, $joinedAt->startOfDay()->diffInDays($expiresAt->copy()->startOfDay()))
        : 365;
    $elapsedDays = $joinedAt ? max(0, $joinedAt->startOfDay()->diffInDays(now()->startOfDay())) : 0;
    $progressPercent = min(100, max(0, (int) round(($elapsedDays / $totalDays) * 100)));

    $paymentHistory = collect();
    if ($member && Schema::hasTable('cashier_transactions')) {
        $paymentQuery = CashierTransaction::query()
            ->where('gym_member_id', $member->id);

        if (Schema::hasColumn('cashier_transactions', 'product_id')) {
            $paymentQuery->whereNull('product_id');
        }

        $paymentHistory = $paymentQuery
            ->latest('transaction_at')
            ->limit(5)
            ->get();
    }

    $latestPayment = $paymentHistory->first();

    return view('member.membership', [
        'user' => $user,
        'member' => $member,
        'membershipStatus' => $membershipStatus,
        'remainingDays' => $remainingDays,
        'totalDays' => $totalDays,
        'progressPercent' => $progressPercent,
        'paymentHistory' => $paymentHistory,
        'latestPayment' => $latestPayment,
    ]);
})->name('membership');

Route::get('/membership/renew', function () {
    return redirect()
        ->route('membership')
        ->with('status', 'Perpanjangan membership sekarang hanya dilayani lewat kasir.');
})->name('membership.renew');

Route::post('/membership/renew/plan', function (Request $request) {
    return redirect()
        ->route('membership')
        ->with('status', 'Perpanjangan membership sekarang hanya dilayani lewat kasir.');
})->name('membership.renew.plan');

Route::get('/membership/renew/payment', function (Request $request) {
    return redirect()
        ->route('membership')
        ->with('status', 'Perpanjangan membership sekarang hanya dilayani lewat kasir.');
})->name('membership.renew.payment');

Route::post('/membership/renew/payment', function (Request $request) {
    return redirect()
        ->route('membership')
        ->with('status', 'Perpanjangan membership sekarang hanya dilayani lewat kasir.');
})->name('membership.renew.payment.submit');

Route::get('/profile', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];
    $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
    $membershipStatus = collect([
        $member?->member_status,
        $member?->membership_plan,
        $member?->status,
    ])->first(fn ($value) => filled($value)) ?? 'member';

    return view('member.profile', [
        'user' => $user,
        'member' => $member,
        'totalCheckins' => $totalCheckins,
        'membershipStatus' => $membershipStatus,
        'photoChangesRemaining' => max(0, 3 - (int) ($member?->profile_photo_change_count ?? 0)),
        'pendingPhotoRequest' => $member
            ? ProfilePhotoChangeRequest::query()
                ->where('gym_member_id', $member->id)
                ->where('status', 'pending')
                ->latest()
                ->first()
            : null,
    ]);
})->name('profile');

Route::post('/profile', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $user = $session['user'];
    $member = $session['member'];

    $validated = $request->validate([
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user?->id),
        ],
        'phone' => ['nullable', 'string', 'max:30'],
        'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    if ($user) {
        $user->email = $validated['email'];

        $user->save();
    }

    if ($member) {
        $member->email = $validated['email'];
        $member->phone = $validated['phone'] ?? null;

        if ($request->hasFile('profile_photo')) {
            $changeCount = (int) ($member->profile_photo_change_count ?? 0);

            if ($changeCount < 3) {
                if ($member->profile_photo_path) {
                    Storage::disk('public')->delete($member->profile_photo_path);
                }

                $member->profile_photo_path = $request->file('profile_photo')->store('member-profile-photos', 'public');
                $member->profile_photo_change_count = $changeCount + 1;
            } else {
                $hasPendingRequest = ProfilePhotoChangeRequest::query()
                    ->where('gym_member_id', $member->id)
                    ->where('status', 'pending')
                    ->exists();

                if ($hasPendingRequest) {
                    return redirect()
                        ->route('profile')
                        ->withErrors(['profile_photo' => 'Permintaan ganti foto profil Anda masih menunggu persetujuan admin.']);
                }

                $requestPath = $request->file('profile_photo')->store('member-profile-photo-requests', 'public');

                ProfilePhotoChangeRequest::query()->create([
                    'user_id' => $user?->id,
                    'gym_member_id' => $member->id,
                    'requested_photo_path' => $requestPath,
                    'status' => 'pending',
                ]);

                $member->save();
                $request->session()->put('auth.email', $validated['email']);

                return redirect()
                    ->route('profile')
                    ->with('status', 'Jatah ganti foto profil sudah habis. Foto baru dikirim ke admin untuk persetujuan.');
            }
        }

        $member->save();
    }

    $request->session()->put('auth.email', $validated['email']);

    return redirect()->route('profile')->with('status', 'Profil berhasil diperbarui.');
})->name('profile.update');

Route::post('/profile/photo', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $validated = $request->validate([
        'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $user = $session['user'];
    $member = $session['member'];

    if (! $member) {
        return redirect()->route('profile')->withErrors(['profile_photo' => 'Data member tidak ditemukan.']);
    }

    $changeCount = (int) ($member->profile_photo_change_count ?? 0);
    if ($changeCount < 3) {
        if ($member->profile_photo_path) {
            Storage::disk('public')->delete($member->profile_photo_path);
        }

        $member->profile_photo_path = $request->file('profile_photo')->store('member-profile-photos', 'public');
        $member->profile_photo_change_count = $changeCount + 1;
        $member->save();

        return redirect()->route('profile')->with('status', 'Foto profil berhasil diganti. Sisa ganti langsung: ' . max(0, 3 - ($changeCount + 1)) . 'x.');
    }

    $hasPendingRequest = ProfilePhotoChangeRequest::query()
        ->where('gym_member_id', $member->id)
        ->where('status', 'pending')
        ->exists();

    if ($hasPendingRequest) {
        return redirect()->route('profile')->withErrors(['profile_photo' => 'Permintaan ganti foto profil Anda masih menunggu persetujuan admin.']);
    }

    ProfilePhotoChangeRequest::query()->create([
        'user_id' => $user?->id,
        'gym_member_id' => $member->id,
        'requested_photo_path' => $request->file('profile_photo')->store('member-profile-photo-requests', 'public'),
        'status' => 'pending',
    ]);

    return redirect()->route('profile')->with('status', 'Jatah ganti foto profil sudah habis. Foto baru dikirim ke admin untuk persetujuan.');
})->name('profile.photo.update');

Route::post('/logout', function (Request $request) {
    $request->session()->forget('auth');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('dashboard');
})->name('logout');

Route::post('/feedback', function (Request $request) use ($activeMemberSession) {
    $session = $activeMemberSession($request);
    if (isset($session['redirect'])) {
        return $session['redirect'];
    }

    $validated = $request->validate([
        'category' => ['required', 'string', 'max:100'],
        'message' => ['required', 'string', 'min:5', 'max:2000'],
    ]);

    $user = $session['user'];
    $member = $session['member'];

    MemberFeedback::query()->create([
        'user_id' => $user->id,
        'gym_member_id' => $member?->id,
        'name' => $member?->full_name ?? $user->name,
        'email' => $member?->email ?? $user->email,
        'subject' => $validated['category'],
        'message' => $validated['message'],
        'read_at' => null,
    ]);

    return back()->with('status', 'Kritik & saran berhasil dikirim ke owner.');
})->name('member.feedback.submit');

Route::get('/member-profile-photos/{path}', function (string $path) {
    $relativePath = str_replace(['..', '\\'], '', $path);
    $userStoragePath = storage_path('app/public/' . $relativePath);
    $rootStoragePath = base_path('../storage/app/public/' . $relativePath);

    if (is_file($userStoragePath)) {
        return response()->file($userStoragePath);
    }

    abort_unless(is_file($rootStoragePath), 404);

    return response()->file($rootStoragePath);
})->where('path', '.*')->name('member.profile-photo.show');
