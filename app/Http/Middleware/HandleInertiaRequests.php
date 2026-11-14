<?php

namespace App\Http\Middleware;

use App\Models\Announcement;
use App\Models\MemberFeedback;
use App\Models\ProfilePhotoChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $auth = $request->session()->get('auth');
        $role = $auth['role'] ?? null;

        $memberNotifications = [];
        if ($role === 'member' && ! empty($auth['id']) && Schema::hasTable('users')) {
            $user = User::query()->with('gymMember')->where('id', $auth['id'])->where('role', 'member')->first();
            $member = $user?->gymMember;

            $announcements = collect();
            if ($member && Schema::hasTable('announcements')) {
                $announcements = Announcement::query()
                    ->where('status', 'active')
                    ->where('body', 'like', "[TARGET_MEMBER_ID:{$member->id}]%")
                    ->latest('publish_at')
                    ->get()
                    ->map(fn (Announcement $item) => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'body' => trim(preg_replace('/^\[TARGET_MEMBER_ID:\d+\]\s*/', '', (string) $item->body)),
                        'publish_at' => $item->publish_at?->translatedFormat('d M Y, H:i'),
                    ]);
            }

            $membershipWarning = null;
            if ($member?->expires_at) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false);
                if ($daysLeft >= 0 && $daysLeft <= 7) {
                    $membershipWarning = [
                        'days_left' => $daysLeft,
                        'title' => $daysLeft <= 3 ? 'Peringatan Membership Mendesak' : 'Peringatan Membership',
                        'message' => $daysLeft === 0
                            ? 'Masa aktif membership Anda berakhir hari ini. Silakan hubungi kasir untuk perpanjangan.'
                            : "Masa aktif membership Anda tersisa {$daysLeft} hari. Silakan hubungi kasir untuk perpanjangan.",
                        'level' => $daysLeft <= 3 ? 'urgent' : 'warning',
                    ];
                }
            }

            $channelUrl = config('services.whatsapp.channel_url');
            $showWhatsAppChannelPrompt = (bool) $request->session()->pull('show_whatsapp_channel_prompt', false);

            $memberNotifications = [
                'announcements' => $announcements->values()->all(),
                'membershipWarning' => $membershipWarning,
                'channelUrl' => $channelUrl,
                'shouldShowChannelPrompt' => $showWhatsAppChannelPrompt && filled($channelUrl),
            ];
        }

        $adminBadges = [];
        if (in_array($role, ['admin', 'master_admin'], true)) {
            $adminBadges = [
                'unreadFeedbacks' => Schema::hasTable('member_feedbacks')
                    ? MemberFeedback::query()->whereNull('read_at')->count()
                    : 0,
                'pendingPhotoRequests' => Schema::hasTable('profile_photo_change_requests')
                    ? ProfilePhotoChangeRequest::query()->where('status', 'pending')->count()
                    : 0,
            ];
        }

        return [
            ...parent::share($request),
            'appName' => 'Arena Gym',
            'auth' => $auth ? [
                'id' => $auth['id'] ?? $auth['user_id'] ?? null,
                'name' => $auth['name'] ?? ($auth['login'] ?? 'Pengguna'),
                'login' => $auth['login'] ?? null,
                'email' => $auth['email'] ?? null,
                'role' => $role,
            ] : null,
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'error' => fn () => $request->session()->get('error'),
                'welcome_name' => fn () => $request->session()->get('welcome_name'),
                'reset_link' => fn () => $request->session()->get('reset_link'),
                'member_credentials' => fn () => $request->session()->get('member_credentials'),
                'whatsapp_channel_share' => fn () => $request->session()->get('whatsapp_channel_share'),
            ],
            'memberNotifications' => $memberNotifications,
            'adminBadges' => $adminBadges,
        ];
    }
}

