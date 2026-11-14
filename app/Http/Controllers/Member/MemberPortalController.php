<?php

namespace App\Http\Controllers\Member;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\CashierTransaction;
use App\Models\GymMember;
use App\Models\GymSetting;
use App\Models\MemberFeedback;
use App\Models\MembershipPackage;
use App\Models\ProfilePhotoChangeRequest;
use App\Models\User;
use App\Services\MemberProfilePhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class MemberPortalController extends Controller
{
    private function resolveContext(Request $request): array
    {
        /** @var User $user */
        $user = $request->attributes->get('memberUser');
        /** @var GymMember|null $member */
        $member = $request->attributes->get('gymMember');

        $membershipStatus = collect([
            $member?->member_status,
            $member?->membership_plan,
            $member?->status,
        ])->first(fn ($value) => filled($value)) ?? 'member';

        return [$user, $member, $membershipStatus];
    }

    public function dashboard(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);
        $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
        $thisMonthCheckins = $member
            ? $member->verifiedCheckins()->whereMonth('checked_in_at', now()->month)->whereYear('checked_in_at', now()->year)->count()
            : 0;
        $remainingDays = $member?->expires_at
            ? max(0, (int) now()->startOfDay()->diffInDays($member->expires_at->copy()->startOfDay(), false))
            : null;

        return Inertia::render('Member/Dashboard', [
            'user' => $user,
            'member' => $member,
            'totalCheckins' => $totalCheckins,
            'thisMonthCheckins' => $thisMonthCheckins,
            'remainingDays' => $remainingDays,
            'membershipStatus' => $membershipStatus,
        ]);
    }

    public function statistics(Request $request): Response
    {
        [$user, $member] = $this->resolveContext($request);

        $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
        $thisMonthCheckins = $member
            ? $member->verifiedCheckins()->whereMonth('checked_in_at', now()->month)->whereYear('checked_in_at', now()->year)->count()
            : 0;
        $lastCheckin = $member ? $member->verifiedCheckins()->latest('checked_in_at')->first()?->checked_in_at : null;

        $weeklyStats = collect(range(6, 0))->map(function ($offset) use ($member) {
            $date = now()->copy()->subDays($offset);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->translatedFormat('d M'),
                'value' => $member ? $member->verifiedCheckins()->whereDate('checked_in_at', $date->toDateString())->count() : 0,
            ];
        })->values();

        $monthlyStats = collect(range(5, 0))->map(function ($offset) use ($member) {
            $date = now()->copy()->subMonths($offset);

            return [
                'label' => $date->translatedFormat('M Y'),
                'value' => $member ? $member->verifiedCheckins()->whereYear('checked_in_at', $date->year)->whereMonth('checked_in_at', $date->month)->count() : 0,
            ];
        })->values();

        return Inertia::render('Member/Statistics', [
            'user' => $user,
            'member' => $member,
            'totalCheckins' => $totalCheckins,
            'thisMonthCheckins' => $thisMonthCheckins,
            'lastCheckinFormatted' => $lastCheckin?->translatedFormat('d M Y, H:i'),
            'weeklyStats' => $weeklyStats,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    public function messages(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);

        $announcements = collect();
        if ($member) {
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

        return Inertia::render('Member/Messages', [
            'user' => $user,
            'member' => $member,
            'membershipStatus' => $membershipStatus,
            'announcements' => $announcements,
        ]);
    }

    public function history(Request $request): Response
    {
        [$user, $member] = $this->resolveContext($request);

        $checkins = $member
            ? $member->verifiedCheckins()
                ->latest('checked_in_at')
                ->paginate(10)
                ->through(fn ($item) => [
                    'id' => $item->id,
                    'checked_in_at' => $item->checked_in_at?->translatedFormat('d M Y, H:i'),
                    'checkin_method' => ucfirst(str_replace('_', ' ', (string) ($item->checkin_method ?? 'admin'))),
                    'notes' => $item->notes,
                ])
            : null;

        $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;
        $thisMonthCheckins = $member
            ? $member->verifiedCheckins()->whereMonth('checked_in_at', now()->month)->whereYear('checked_in_at', now()->year)->count()
            : 0;
        $thisWeekCheckins = $member
            ? $member->verifiedCheckins()->whereBetween('checked_in_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
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

        $trainingChart = [];
        for ($date = $chartStart->copy(); $date->lte($chartEnd); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $trainingChart[] = [
                'label' => $date->translatedFormat('d M'),
                'value' => $chartCheckins->get($key, collect())->count(),
            ];
        }

        $trackedDays = max(count($trainingChart), 1);
        $activeTrainingDays = collect($trainingChart)->filter(fn ($row) => $row['value'] > 0)->count();
        $trainingConsistency = (int) round(($activeTrainingDays / $trackedDays) * 100);
        $trainingAssessment = $trainingConsistency >= 50
            ? 'Rajin'
            : ($trainingConsistency >= 25 ? 'Cukup Konsisten' : 'Perlu Lebih Konsisten');

        return Inertia::render('Member/History', [
            'user' => $user,
            'member' => $member,
            'checkins' => $checkins,
            'totalCheckins' => $totalCheckins,
            'thisMonthCheckins' => $thisMonthCheckins,
            'thisWeekCheckins' => $thisWeekCheckins,
            'trainingChart' => $trainingChart,
            'trainingConsistency' => $trainingConsistency,
            'trainingAssessment' => $trainingAssessment,
            'activeTrainingDays' => $activeTrainingDays,
            'trackedTrainingDays' => $trackedDays,
        ]);
    }

    public function barcode(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);

        return Inertia::render('Member/Barcode', [
            'user' => $user,
            'member' => $member,
            'checkinCode' => $member?->checkin_code,
            'membershipStatus' => $membershipStatus,
            'sessionId' => 'ARENA-' . random_int(100000, 999999),
        ]);
    }

    public function membership(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);

        $joinedAt = $member?->joined_at;
        $expiresAt = $member?->expires_at;
        $remainingDays = $expiresAt ? max(0, (int) now()->startOfDay()->diffInDays($expiresAt->copy()->startOfDay(), false)) : null;
        $totalDays = ($joinedAt && $expiresAt)
            ? max(1, $joinedAt->copy()->startOfDay()->diffInDays($expiresAt->copy()->startOfDay()))
            : 30;
        $elapsedDays = $joinedAt ? max(0, $joinedAt->copy()->startOfDay()->diffInDays(now()->startOfDay())) : 0;
        $progressPercent = min(100, max(0, (int) round(($elapsedDays / $totalDays) * 100)));

        $paymentHistory = collect();
        if ($member && Schema::hasTable('cashier_transactions')) {
            $paymentQuery = CashierTransaction::query()->where('gym_member_id', $member->id);
            if (Schema::hasColumn('cashier_transactions', 'product_id')) {
                $paymentQuery->whereNull('product_id');
            }
            $paymentHistory = $paymentQuery->latest('transaction_at')->limit(10)->get()->map(fn (CashierTransaction $trx) => [
                'id' => $trx->id,
                'invoice' => $trx->invoice,
                'transaction_type' => $trx->transaction_type,
                'amount_formatted' => RouteHelpers::formatCurrency($trx->amount),
                'payment_method' => strtoupper((string) $trx->payment_method),
                'payment_status' => $trx->payment_status,
                'transaction_at' => $trx->transaction_at?->translatedFormat('d M Y, H:i'),
            ]);
        }

        return Inertia::render('Member/Membership', [
            'user' => $user,
            'member' => $member,
            'membershipStatus' => $membershipStatus,
            'remainingDays' => $remainingDays,
            'totalDays' => $totalDays,
            'progressPercent' => $progressPercent,
            'joinedAtFormatted' => $joinedAt?->translatedFormat('d M Y') ?? '-',
            'expiresAtFormatted' => $expiresAt?->translatedFormat('d M Y') ?? '-',
            'paymentHistory' => $paymentHistory,
        ]);
    }

    public function profile(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);
        $totalCheckins = $member ? $member->verifiedCheckins()->count() : 0;

        $pendingPhotoRequest = $member
            ? ProfilePhotoChangeRequest::query()
                ->where('gym_member_id', $member->id)
                ->where('status', 'pending')
                ->latest()
                ->first()
            : null;

        return Inertia::render('Member/Profile', [
            'user' => $user,
            'member' => $member,
            'totalCheckins' => $totalCheckins,
            'membershipStatus' => $membershipStatus,
            'photoChangesRemaining' => max(0, MemberProfilePhotoService::MAX_DIRECT_CHANGES - (int) ($member?->profile_photo_change_count ?? 0)),
            'pendingPhotoRequest' => $pendingPhotoRequest ? [
                'id' => $pendingPhotoRequest->id,
                'status' => $pendingPhotoRequest->status,
                'created_at' => $pendingPhotoRequest->created_at?->translatedFormat('d M Y, H:i'),
            ] : null,
        ]);
    }

    public function updateProfile(Request $request, MemberProfilePhotoService $photoService): RedirectResponse
    {
        [$user, $member] = $this->resolveContext($request);

        $validated = $request->validate([
            'name'          => ['nullable', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'         => ['nullable', 'string', 'max:30'],
            'gender'        => ['nullable', 'in:male,female'],
            'birth_date'    => ['nullable', 'date'],
            'address'       => ['nullable', 'string', 'max:500'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->email = $validated['email'];
        if (! empty($validated['name'])) {
            $user->name = $validated['name'];
        }
        $user->save();

        if ($member) {
            $member->email = $validated['email'];
            $member->phone = $validated['phone'] ?? null;
            if (! empty($validated['name'])) {
                $member->full_name = $validated['name'];
            }
            if (isset($validated['gender'])) {
                $member->gender = $validated['gender'];
            }
            if (isset($validated['birth_date'])) {
                $member->birth_date = $validated['birth_date'];
            }
            if (isset($validated['address'])) {
                $member->address = $validated['address'];
            }
            $member->save();

            if ($request->hasFile('profile_photo')) {
                $result = $photoService->handleUpload($user, $member, $request->file('profile_photo'));
                $request->session()->put('auth.email', $validated['email']);
                if (! empty($validated['name'])) {
                    $request->session()->put('auth.name', $validated['name']);
                }

                if (! $result['ok']) {
                    return redirect()->route('member.profile')->withErrors(['profile_photo' => $result['error']]);
                }

                return redirect()->route('member.profile')->with('status', $result['message']);
            }
        }

        $request->session()->put('auth.email', $validated['email']);
        if (! empty($validated['name'])) {
            $request->session()->put('auth.name', $validated['name']);
        }

        return redirect()->route('member.profile')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request, MemberProfilePhotoService $photoService): RedirectResponse
    {
        [$user, $member] = $this->resolveContext($request);

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (! $member) {
            return redirect()->route('member.profile')->withErrors(['profile_photo' => 'Data member tidak ditemukan.']);
        }

        $result = $photoService->handleUpload($user, $member, $request->file('profile_photo'));

        if (! $result['ok']) {
            return redirect()->route('member.profile')->withErrors(['profile_photo' => $result['error']]);
        }

        return redirect()->route('member.profile')->with('status', $result['message']);
    }

    public function submitFeedback(Request $request): RedirectResponse
    {
        [$user, $member] = $this->resolveContext($request);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

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
    }

    public function packages(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);

        $packages = MembershipPackage::query()
            ->where('is_active', true)
            ->orderBy('duration_days')
            ->get();

        return Inertia::render('Member/Packages', [
            'user' => $user,
            'member' => $member,
            'membershipStatus' => $membershipStatus,
            'packages' => $packages,
        ]);
    }

    public function payments(Request $request): Response
    {
        [$user, $member, $membershipStatus] = $this->resolveContext($request);

        $transactions = collect();
        if ($member) {
            $transactions = CashierTransaction::query()
                ->where('gym_member_id', $member->id)
                ->with(['package', 'cashier:id,name'])
                ->latest('transaction_at')
                ->latest('id')
                ->paginate(15)
                ->withQueryString();
        }

        $settings = GymSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Member/Payments', [
            'user' => $user,
            'member' => $member,
            'membershipStatus' => $membershipStatus,
            'transactions' => $transactions,
            'gymProfile' => [
                'name'    => $settings['gym_name'] ?? 'Arena Gym & Fitness',
                'phone'   => $settings['gym_phone'] ?? '0812-3456-7890',
                'address' => $settings['gym_address'] ?? 'Jl. Arena Olahraga No. 10',
                'footer'  => $settings['invoice_footer'] ?? 'Terima kasih atas kunjungan Anda.',
            ],
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->attributes->get('memberUser');

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record(
            action: 'change_password',
            userId: $user->id,
            description: "Member {$user->name} berhasil mengubah password akun."
        );

        return back()->with('status', 'Password Anda berhasil diperbarui!');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }
}

