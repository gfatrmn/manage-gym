<?php

namespace App\Helpers;

use App\Models\CashierTransaction;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Kumpulan helper function yang dipakai bersama-sama di seluruh route
 * admin maupun cashier. Semua method bersifat static agar mudah dipanggil
 * tanpa perlu instantiasi.
 */
class RouteHelpers
{
    // ─── Auth ────────────────────────────────────────────────────────────────

    public static function redirectByRole(): \Illuminate\Http\RedirectResponse
    {
        return match (session('auth.role')) {
            'admin'        => redirect()->route('admin.dashboard'),
            'cashier'      => redirect()->route('cashier.dashboard'),
            'master_admin' => redirect()->route('admin.dashboard'),
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

    // ─── Formatting ──────────────────────────────────────────────────────────

    public static function formatCurrency(?int $amount): string
    {
        return 'Rp' . number_format((int) $amount, 0, ',', '.');
    }

    public static function generateInvoice(string $prefix): string
    {
        return strtoupper($prefix) . '-' . now()->format('YmdHis') . random_int(100, 999);
    }

    // ─── Membership expiry ───────────────────────────────────────────────────

    /**
     * Hitung tanggal kadaluarsa 1 bulan dari $baseDate (atau hari ini).
     */
    public static function calculateMonthlyExpiryDate(?Carbon $baseDate): string
    {
        return ($baseDate ?? Carbon::today())
            ->copy()
            ->startOfDay()
            ->addMonthNoOverflow()
            ->toDateString();
    }

    /**
     * Hitung tanggal perpanjangan membership:
     * - Jika membership masih aktif → perpanjang dari tanggal expire.
     * - Jika sudah expire / belum punya → perpanjang dari hari ini.
     */
    public static function calculateMembershipRenewalExpiry(GymMember $member, ?Carbon $today = null): string
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        if ($member->expires_at && $member->expires_at->copy()->startOfDay()->gte($today)) {
            return self::calculateMonthlyExpiryDate($member->expires_at->copy());
        }

        return self::calculateMonthlyExpiryDate($today);
    }

    // ─── Check-in code ───────────────────────────────────────────────────────

    public static function generateMemberCheckinCode(): string
    {
        do {
            $code = 'AGM-' . strtoupper(Str::random(10));
        } while (GymMember::query()->where('checkin_code', $code)->exists());

        return $code;
    }

    // ─── Member lifecycle status ──────────────────────────────────────────────

    public static function memberLifecycleStatus(?GymMember $member): array
    {
        $today          = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);
        $expiresAt      = $member?->expires_at;

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

    // ─── Store check-in (dipakai admin, cashier, & QR self-service) ──────────

    public static function storeMemberCheckin(
        Request $request,
        string $actor = 'admin',
        string $redirectRoute = 'admin.checkins',
        array $redirectParams = []
    ): \Illuminate\Http\RedirectResponse {
        $validated = $request->validate([
            'gym_member_id'  => ['nullable', 'exists:gym_members,id', 'required_without_all:checkin_code,submitted_phone'],
            'checkin_code'   => ['nullable', 'string', 'max:40', 'required_without_all:gym_member_id,submitted_phone'],
            'submitted_name' => ['nullable', 'string', 'max:255'],
            'submitted_phone'=> ['nullable', 'string', 'max:30', 'required_without_all:gym_member_id,checkin_code'],
            'checkin_date'   => ['nullable', 'date'],
            'checkin_time'   => ['nullable', 'date_format:H:i'],
            'notes'          => ['nullable', 'string'],
        ]);

        $resolvedCheckinCode = strtoupper(trim((string) ($validated['checkin_code'] ?? '')));
        $resolvedPhone       = preg_replace('/\D+/', '', (string) ($validated['submitted_phone'] ?? ''));

        $member = null;

        if (! empty($validated['gym_member_id'])) {
            $member = GymMember::query()->find($validated['gym_member_id']);
        } elseif (! empty($resolvedCheckinCode)) {
            $member = GymMember::query()->where('checkin_code', $resolvedCheckinCode)->first();
        } elseif ($actor === 'qr_member' && ! empty($resolvedPhone)) {
            $member = GymMember::query()
                ->where('member_status', 'member')
                ->whereNotNull('phone')
                ->get()
                ->first(fn (GymMember $candidate) => preg_replace('/\D+/', '', (string) $candidate->phone) === $resolvedPhone);
        }

        $errorKey = ! empty($resolvedCheckinCode)
            ? 'checkin_code'
            : (! empty($validated['gym_member_id']) ? 'gym_member_id' : 'submitted_phone');

        if (! $member || ($member->member_status !== 'member' && ! in_array($actor, ['cashier', 'admin'], true))) {
            return redirect()->route($redirectRoute, $redirectParams)
                ->withErrors([$errorKey => 'Member untuk check-in tidak ditemukan.'])
                ->withInput();
        }

        if (! $member->expires_at || $member->expires_at->lt(Carbon::today())) {
            return redirect()->route($redirectRoute, $redirectParams)
                ->withErrors([$errorKey => 'Membership member ini sudah expired dan tidak bisa check-in.'])
                ->withInput();
        }

        $isQrMemberCheckin = $actor === 'qr_member';

        if ($isQrMemberCheckin) {
            $request->validate([
                'submitted_name'  => ['required', 'string', 'max:255'],
                'submitted_phone' => ['required', 'string', 'max:30'],
            ]);
        }

        GymCheckin::create([
            'gym_member_id'       => $member->id,
            'checked_in_at'       => (! empty($validated['checkin_date']) && ! empty($validated['checkin_time']))
                ? Carbon::parse($validated['checkin_date'] . ' ' . $validated['checkin_time'])
                : now(),
            'checkin_method'      => $actor,
            'verification_status' => $isQrMemberCheckin ? 'pending' : 'verified',
            'submitted_name'      => $validated['submitted_name'] ?? null,
            'submitted_phone'     => $validated['submitted_phone'] ?? null,
            'verified_at'         => $isQrMemberCheckin ? null : now(),
            'verified_by'         => $isQrMemberCheckin ? null : (string) (session('auth.name') ?? session('auth.login') ?? $actor),
            'notes'               => $validated['notes'] ?? null,
        ]);

        if ($isQrMemberCheckin) {
            return redirect()->route($redirectRoute, $redirectParams)
                ->with('status', "Pengajuan check-in untuk {$member->full_name} sudah dikirim. Tunggu kasir memvalidasi sebelum masuk.");
        }

        $successLabel = match ($actor) {
            'cashier'    => 'kasir',
            'qr_member'  => 'mandiri',
            default      => 'admin',
        };

        return redirect()->route($redirectRoute, $redirectParams)
            ->with('status', "Check-in {$successLabel} untuk {$member->full_name} berhasil dicatat.");
    }

    // ─── Page meta ───────────────────────────────────────────────────────────

    public static function pageMeta(string $key): array
    {
        $map = [
            'dashboard' => [
                'pageTitle'          => 'Dashboard Admin Arena Gym',
                'activePage'         => 'dashboard',
                'sidebarStatusTitle' => 'Operasional Stabil',
                'sidebarStatusNote'  => 'Pantau semua modul admin dari halaman utama.',
                'icon'               => 'layout-dashboard',
                'heroImage'          => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=1200',
            ],
            'members' => [
                'pageTitle'          => 'Member - Arena Gym',
                'activePage'         => 'members',
                'sidebarStatusTitle' => 'Data Member',
                'sidebarStatusNote'  => 'Fokus pada member dengan paket aktif atau expired.',
                'icon'               => 'users',
                'heroImage'          => 'https://images.unsplash.com/photo-1571012281386-7a0c1645b252?auto=format&fit=crop&q=80&w=1200',
            ],
            'non-members' => [
                'pageTitle'          => 'Non Member - Arena Gym',
                'activePage'         => 'non-members',
                'sidebarStatusTitle' => 'Data Tamu',
                'sidebarStatusNote'  => 'Pantau tamu bayar sekali dan trial secara terpisah.',
                'icon'               => 'user-plus',
                'heroImage'          => 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?auto=format&fit=crop&q=80&w=1200',
            ],
            'products' => [
                'pageTitle'          => 'Produk - Arena Gym',
                'activePage'         => 'products',
                'sidebarStatusTitle' => 'Master Produk',
                'sidebarStatusNote'  => 'Kelola suplemen dan vitamin untuk kebutuhan penjualan kasir.',
                'icon'               => 'package',
                'heroImage'          => 'https://images.unsplash.com/photo-1583454110333-a006bb6d9894?auto=format&fit=crop&q=80&w=1200',
            ],
            'checkins' => [
                'pageTitle'          => 'Check-in - Arena Gym',
                'activePage'         => 'checkins',
                'sidebarStatusTitle' => 'Check-in Aktif',
                'sidebarStatusNote'  => 'Aktivitas kunjungan harian terus diperbarui.',
                'icon'               => 'clipboard-check',
                'heroImage'          => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=1200',
            ],
            'announcements' => [
                'pageTitle'          => 'Pengumuman - Arena Gym',
                'activePage'         => 'announcements',
                'sidebarStatusTitle' => 'Info Aktif',
                'sidebarStatusNote'  => 'Pengumuman penting siap dipublikasikan ke member.',
                'icon'               => 'megaphone',
                'heroImage'          => 'https://images.unsplash.com/photo-1483721310020-03333e577078?auto=format&fit=crop&q=80&w=1200',
            ],
            'reports' => [
                'pageTitle'          => 'Laporan - Arena Gym',
                'activePage'         => 'reports',
                'sidebarStatusTitle' => 'Laporan Siap',
                'sidebarStatusNote'  => 'Ringkasan harian dapat diakses dan diunduh.',
                'icon'               => 'bar-chart-3',
                'heroImage'          => 'https://images.unsplash.com/photo-1554224158-b67a1c00983f?auto=format&fit=crop&q=80&w=1200',
            ],
        ];

        return $map[$key] ?? [];
    }

    // ─── Cashier view data builder ────────────────────────────────────────────

    public static function buildCashierViewData(array $overrides = []): array
    {
        $today = Carbon::today();

        $transactions = CashierTransaction::query()
            ->with(['member', 'product'])
            ->latest('transaction_at')
            ->get();

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

        $memberPayments = $transactions->where('transaction_group', 'member_payment')->values();
        $dailyPayments  = $transactions->where('transaction_group', 'daily_pass')->values();

        $verifiedTransactions     = $transactions->filter(fn (CashierTransaction $t) => $t->payment_status === 'verified')->values();
        $todayTransactions        = $transactions->filter(fn (CashierTransaction $t) => $t->transaction_at->isToday())->values();
        $todayVerifiedTransactions = $todayTransactions->filter(fn (CashierTransaction $t) => $t->payment_status === 'verified')->values();
        $todayPendingTransactions  = $todayTransactions->filter(fn (CashierTransaction $t) => $t->payment_status !== 'verified')->values();
        $todayRevenue              = $todayVerifiedTransactions->sum('amount');

        $paymentMethodSummary = collect(['cash', 'qris'])
            ->map(function (string $method) use ($verifiedTransactions) {
                $amount = $verifiedTransactions->where('payment_method', $method)->sum('amount');

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

        return array_merge([
            'pageTitle'           => 'Dashboard Kasir Arena Gym',
            'activePage'          => 'cashier.dashboard',
            'sidebarStatusTitle'  => 'Shift Kasir Aktif',
            'sidebarStatusNote'   => 'Pantau pembayaran, transaksi, dan bukti pembayaran harian.',
            'cashierShift'        => ['start' => '08:00', 'end' => '16:00', 'label' => '08:00 - 16:00'],
            'cashierCheckinMembers' => GymMember::query()
                ->where(function ($q) use ($today) {
                    $q->where('member_status', 'non_member')
                        ->orWhere(function ($q) use ($today) {
                            $q->where('member_status', 'member')
                                ->whereDate('expires_at', '>=', $today);
                        });
                })
                ->orderBy('full_name')
                ->get(),
            'cashierTodayCheckins'   => $todayCheckins,
            'cashierPendingCheckins' => $pendingCheckins,
            'cashierLatestCheckin'   => GymCheckin::query()
                ->with('member')
                ->where('verification_status', 'verified')
                ->latest('checked_in_at')
                ->first(),
            'cashierStats' => [
                ['label' => 'Pembayaran Member',        'value' => number_format($memberPayments->count(), 0, ',', '.'), 'change' => $memberPayments->where('payment_status', 'verified')->count() . ' terverifikasi'],
                ['label' => 'Pembayaran Daily Pass',    'value' => number_format($dailyPayments->count(), 0, ',', '.'),  'change' => $dailyPayments->where('payment_status', 'verified')->count() . ' terverifikasi'],
                ['label' => 'Total Transaksi Hari Ini', 'value' => number_format($todayTransactions->count(), 0, ',', '.'), 'change' => $todayPendingTransactions->count() . ' pending'],
                ['label' => 'Pendapatan Hari Ini',      'value' => self::formatCurrency($todayRevenue), 'change' => $todayVerifiedTransactions->count() . ' transaksi lunas'],
            ],
            'transactions'   => $transactions,
            'memberPayments' => $memberPayments,
            'dailyPayments'  => $dailyPayments,
            'receiptQueue'   => $transactions->values(),
            'paymentMethods' => $paymentMethods,
        ], $overrides);
    }
}
