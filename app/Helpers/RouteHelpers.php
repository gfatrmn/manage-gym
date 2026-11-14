<?php

namespace App\Helpers;

use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\MemberHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Kumpulan helper function yang dipakai bersama-sama di seluruh modul
 * admin, kasir, dan member.
 */
class RouteHelpers
{
    public static function redirectByRole(): \Illuminate\Http\RedirectResponse
    {
        return match (session('auth.role')) {
            'admin'        => redirect()->route('admin.dashboard'),
            'cashier'      => redirect()->route('cashier.dashboard'),
            'master_admin' => redirect()->route('admin.dashboard'),
            'member'       => redirect()->route('member.dashboard'),
            default        => redirect()->route('login'),
        };
    }

    public static function ensureAdmin(): ?\Illuminate\Http\RedirectResponse
    {
        if (! in_array(session('auth.role'), ['admin', 'master_admin'], true)) {
            return redirect()->route('admin.login');
        }

        return null;
    }

    public static function ensureCashier(): ?\Illuminate\Http\RedirectResponse
    {
        if (! in_array(session('auth.role'), ['cashier', 'master_admin'], true)) {
            return redirect()->route('cashier.login');
        }

        return null;
    }

    public static function ensureMember(): ?\Illuminate\Http\RedirectResponse
    {
        if (session('auth.role') !== 'member') {
            return redirect()->route('member.login');
        }

        return null;
    }

    public static function formatCurrency(?int $amount): string
    {
        return 'Rp' . number_format((int) $amount, 0, ',', '.');
    }

    public static function generateInvoice(string $prefix = 'INV'): string
    {
        $prefix = strtoupper(trim($prefix));
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)) . random_int(10, 99);
    }

    public static function generateMemberId(): string
    {
        do {
            $code = 'MBR-' . now()->format('ym') . '-' . strtoupper(Str::random(4));
        } while (GymMember::query()->where('checkin_code', $code)->exists());

        return $code;
    }

    /**
     * Menghitung tanggal mulai dan tanggal berakhir membership sesuai Business Rule BR-011:
     * - Jika member masih aktif (expires_at >= today): start_date = 1 hari setelah expires_at saat ini.
     * - Jika member expired atau belum pernah aktif: start_date = hari ini.
     * - end_date dihitung: start_date + duration_days - 1 hari (inklusif).
     */
    public static function calculateMembershipDates(GymMember $member, int $durationDays = 30): array
    {
        $today = Carbon::today();
        $durationDays = max(1, $durationDays);

        if ($member->expires_at && $member->expires_at->copy()->startOfDay()->gte($today)) {
            $startDate = $member->expires_at->copy()->startOfDay()->addDay();
        } else {
            $startDate = $today->copy();
        }

        $endDate = $startDate->copy()->addDays($durationDays - 1);

        return [
            'start_date' => $startDate->toDateString(),
            'end_date'   => $endDate->toDateString(),
        ];
    }

    public static function calculateMonthlyExpiryDate(?Carbon $baseDate, int $months = 1): string
    {
        return ($baseDate ?? Carbon::today())
            ->copy()
            ->startOfDay()
            ->addMonthsNoOverflow(max(1, $months))
            ->toDateString();
    }

    public static function calculateMembershipRenewalExpiry(GymMember $member, ?Carbon $today = null, int $months = 1): string
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        if ($member->expires_at && $member->expires_at->copy()->startOfDay()->gte($today)) {
            return self::calculateMonthlyExpiryDate($member->expires_at->copy(), $months);
        }

        return self::calculateMonthlyExpiryDate($today, $months);
    }

    public static function generateMemberCheckinCode(): string
    {
        do {
            $code = 'AGM-' . strtoupper(Str::random(10));
        } while (GymMember::query()->where('checkin_code', $code)->exists());

        return $code;
    }

    public static function memberLifecycleStatus(?GymMember $member): array
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);
        $expiresAt = $member?->expires_at?->copy()->startOfDay();

        if (! $expiresAt) {
            return ['key' => 'unknown', 'label' => 'Tidak diketahui', 'color' => 'secondary'];
        }

        if ($expiresAt->lt($today)) {
            return ['key' => 'expired', 'label' => 'Expired', 'color' => 'danger'];
        }

        if ($expiresAt->betweenIncluded($today, $sevenDaysFromNow)) {
            return ['key' => 'expiring', 'label' => 'Akan Expired', 'color' => 'warning'];
        }

        return ['key' => 'active', 'label' => 'Aktif', 'color' => 'success'];
    }

    public static function storeMemberCheckin(
        Request $request,
        string $actor = 'admin',
        string $redirectRoute = 'admin.checkins',
        array $redirectParams = []
    ): \Illuminate\Http\RedirectResponse {
        $validated = $request->validate([
            'gym_member_id'   => ['nullable', 'exists:gym_members,id', 'required_without_all:checkin_code,submitted_phone'],
            'checkin_code'    => ['nullable', 'string', 'max:40', 'required_without_all:gym_member_id,submitted_phone'],
            'submitted_name'  => ['nullable', 'string', 'max:255'],
            'submitted_phone' => ['nullable', 'string', 'max:30', 'required_without_all:gym_member_id,checkin_code'],
            'checkin_date'    => ['nullable', 'date'],
            'checkin_time'    => ['nullable', 'date_format:H:i'],
            'notes'           => ['nullable', 'string'],
        ]);

        $resolvedCheckinCode = strtoupper(trim((string) ($validated['checkin_code'] ?? '')));
        $resolvedPhone = preg_replace('/\D+/', '', (string) ($validated['submitted_phone'] ?? ''));

        $member = null;

        if (! empty($validated['gym_member_id'])) {
            $member = GymMember::query()->find($validated['gym_member_id']);
        } elseif ($resolvedCheckinCode !== '') {
            $member = GymMember::query()->where('checkin_code', $resolvedCheckinCode)->first();
        } elseif ($actor === 'qr_member' && $resolvedPhone !== '') {
            $member = GymMember::query()
                ->whereNotNull('phone')
                ->where('status', 'member')
                ->get(['id', 'full_name', 'phone', 'expires_at', 'checkin_code', 'status'])
                ->first(fn (GymMember $candidate) => preg_replace('/\D+/', '', (string) $candidate->phone) === $resolvedPhone);
        }

        $errorKey = $resolvedCheckinCode !== ''
            ? 'checkin_code'
            : (! empty($validated['gym_member_id']) ? 'gym_member_id' : 'submitted_phone');

        if (! $member) {
            return redirect()->route($redirectRoute, $redirectParams)
                ->withErrors([$errorKey => 'Member untuk check-in tidak ditemukan.'])
                ->withInput();
        }

        if (! $member->expires_at || $member->expires_at->copy()->startOfDay()->lt(Carbon::today())) {
            return redirect()->route($redirectRoute, $redirectParams)
                ->withErrors([$errorKey => 'Membership member ini sudah expired dan tidak bisa check-in.'])
                ->withInput();
        }

        $isQrMemberCheckin = $actor === 'qr_member';
        $verificationStatus = ($isQrMemberCheckin && $redirectRoute === 'member.checkin') ? 'pending' : 'verified';

        if ($isQrMemberCheckin && $resolvedCheckinCode === '') {
            $request->validate([
                'submitted_name'  => ['required', 'string', 'max:255'],
                'submitted_phone' => ['required', 'string', 'max:30'],
            ]);
        }

        $successLabel = match ($actor) {
            'cashier'   => 'kasir',
            'qr_member' => 'QR',
            default     => 'admin',
        };

        $checkinPayload = [
            'gym_member_id'       => $member->id,
            'checked_in_at'       => (! empty($validated['checkin_date']) && ! empty($validated['checkin_time']))
                ? Carbon::parse($validated['checkin_date'] . ' ' . $validated['checkin_time'])
                : now(),
            'checkin_method'      => $actor,
            'verification_status' => $verificationStatus,
            'submitted_name'      => $validated['submitted_name'] ?? null,
            'submitted_phone'     => $validated['submitted_phone'] ?? null,
            'verified_at'         => $verificationStatus === 'verified' ? now() : null,
            'verified_by'         => $verificationStatus === 'verified' ? (string) (session('auth.name') ?? session('auth.login') ?? $actor) : null,
        ];

        if (Schema::hasColumn('gym_checkins', 'notes')) {
            $checkinPayload['notes'] = $validated['notes'] ?? null;
        }

        DB::transaction(function () use ($checkinPayload, $verificationStatus, $member, $successLabel) {
            $checkin = GymCheckin::create($checkinPayload);

            if ($verificationStatus === 'verified') {
                MemberHistory::create([
                    'gym_member_id' => $member->id,
                    'history_type'  => 'checkin',
                    'occurred_at'   => $checkin->checked_in_at,
                    'title'         => 'Check-in member',
                    'description'   => 'Check-in melalui ' . $successLabel,
                    'source_type'   => GymCheckin::class,
                    'source_id'     => $checkin->id,
                ]);
            }
        });

        return redirect()->route($redirectRoute, $redirectParams)
            ->with('status', "Check-in {$successLabel} untuk {$member->full_name} berhasil dicatat.")
            ->with('welcome_name', $member->full_name);
    }

    public static function buildCashierViewData(array $overrides = []): array
    {
        $today = Carbon::today();
        $startOfToday = $today->copy()->startOfDay();
        $endOfToday = $today->copy()->endOfDay();

        $todayTransactionsQuery = CashierTransaction::query()
            ->whereBetween('transaction_at', [$startOfToday, $endOfToday]);

        $todayMemberCount = (clone $todayTransactionsQuery)->where('transaction_group', 'member_payment')->count();
        $todayMemberVerifiedCount = (clone $todayTransactionsQuery)->where('transaction_group', 'member_payment')->where('payment_status', 'verified')->count();
        $todayDailyCount = (clone $todayTransactionsQuery)->where('transaction_group', 'daily_pass')->count();
        $todayDailyVerifiedCount = (clone $todayTransactionsQuery)->where('transaction_group', 'daily_pass')->where('payment_status', 'verified')->count();
        $todayTotalCount = (clone $todayTransactionsQuery)->count();
        $todayPendingCount = (clone $todayTransactionsQuery)->where('payment_status', '!=', 'verified')->count();
        $todayVerifiedCount = (clone $todayTransactionsQuery)->where('payment_status', 'verified')->count();
        $todayRevenue = (int) (clone $todayTransactionsQuery)->where('payment_status', 'verified')->sum('amount');

        $paymentMethodBreakdown = CashierTransaction::query()
            ->whereBetween('transaction_at', [$startOfToday, $endOfToday])
            ->where('payment_status', 'verified')
            ->whereIn('payment_method', ['cash', 'qris'])
            ->selectRaw('payment_method, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->pluck('total_amount', 'payment_method');

        $paymentMethodSummary = collect(['cash', 'qris'])
            ->map(function (string $method) use ($paymentMethodBreakdown) {
                $amount = (int) ($paymentMethodBreakdown[$method] ?? 0);

                return [
                    'label'  => strtoupper($method),
                    'value'  => self::formatCurrency($amount),
                    'amount' => $amount,
                ];
            })
            ->filter(fn (array $item) => $item['amount'] > 0)
            ->values();

        $paymentMethodTotal = max($paymentMethodSummary->sum('amount'), 1);

        $paymentMethods = $paymentMethodSummary
            ->map(fn (array $item) => [
                'label'    => $item['label'],
                'value'    => $item['value'],
                'progress' => (int) round(($item['amount'] / $paymentMethodTotal) * 100),
                'color'    => 'danger',
            ])
            ->values()
            ->all();

        $todayCheckins = GymCheckin::query()
            ->with('member')
            ->whereDate('checked_in_at', $today)
            ->where('verification_status', 'verified')
            ->latest('checked_in_at')
            ->get();

        $pendingCheckins = GymCheckin::query()
            ->with('member')
            ->where('verification_status', 'pending')
            ->latest('checked_in_at')
            ->get();

        $recentPendingQris = CashierTransaction::query()
            ->with(['member', 'product'])
            ->where('payment_method', 'qris')
            ->where('payment_status', '!=', 'verified')
            ->latest('transaction_at')
            ->limit(15)
            ->get();

        return array_merge([
            'pageTitle'          => 'Dashboard Kasir Arena Gym',
            'activePage'         => 'cashier.dashboard',
            'sidebarStatusTitle' => 'Shift Kasir Aktif',
            'sidebarStatusNote'  => 'Pantau pembayaran, transaksi, dan bukti pembayaran harian.',
            'cashierShift'       => ['start' => '08:00', 'end' => '16:00', 'label' => '08:00 - 16:00'],
            'cashierCheckinMembers' => GymMember::query()
                ->where('status', 'member')
                ->whereDate('expires_at', '>=', $today)
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'phone', 'checkin_code', 'expires_at', 'profile_photo_path']),
            'cashierTodayCheckins'   => $todayCheckins,
            'cashierPendingCheckins' => $pendingCheckins,
            'cashierLatestCheckin'   => GymCheckin::query()
                ->with('member')
                ->where('verification_status', 'verified')
                ->latest('checked_in_at')
                ->first(),
            'cashierStats' => [
                ['label' => 'Pembayaran Member',        'value' => number_format($todayMemberCount, 0, ',', '.'), 'change' => $todayMemberVerifiedCount . ' terverifikasi'],
                ['label' => 'Pembayaran Daily Pass',    'value' => number_format($todayDailyCount, 0, ',', '.'),  'change' => $todayDailyVerifiedCount . ' terverifikasi'],
                ['label' => 'Total Transaksi Hari Ini', 'value' => number_format($todayTotalCount, 0, ',', '.'), 'change' => $todayPendingCount . ' pending'],
                ['label' => 'Pendapatan Hari Ini',      'value' => self::formatCurrency($todayRevenue), 'change' => $todayVerifiedCount . ' transaksi lunas'],
            ],
            'recentPendingQris' => $recentPendingQris,
            'paymentMethods'    => $paymentMethods,
        ], $overrides);
    }
}
