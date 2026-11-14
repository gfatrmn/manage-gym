<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RouteHelpers;
use App\Http\Controllers\Controller;
use App\Models\DailyGuest;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CheckinController extends Controller
{
    public function index(Request $request): Response
    {
        $startOfToday = now()->startOfDay();
        $endOfToday = now()->endOfDay();

        $todayCheckinsCount = GymCheckin::query()
            ->where('verification_status', 'verified')
            ->whereBetween('checked_in_at', [$startOfToday, $endOfToday])
            ->count();

        $todayDailyPassCount = DailyGuest::query()
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->count();

        $todayDailyPassRevenue = (int) DailyGuest::query()
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->sum('payment_amount');

        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : null;
        $typeFilter = $request->input('type');

        $memberLogs = collect();
        if (! $typeFilter || $typeFilter === 'member') {
            $memberQuery = GymCheckin::query()
                ->with('member')
                ->where('verification_status', 'verified')
                ->latest('checked_in_at');

            if ($dateFrom) {
                $memberQuery->where('checked_in_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $memberQuery->where('checked_in_at', '<=', $dateTo);
            }

            $memberLogs = $memberQuery->get()->map(fn (GymCheckin $item) => [
                'id' => 'checkin-' . $item->id,
                'type' => 'member',
                'nama' => $item->member?->full_name ?? 'N/A',
                'sub' => $item->member?->checkin_code ?? '',
                'info' => 'Aktif hingga: ' . ($item->member?->expires_at?->format('d M Y') ?? '-'),
                'waktu' => $item->checked_in_at?->translatedFormat('d M Y, H:i') ?? '-',
                'waktu_raw' => $item->checked_in_at?->timestamp ?? 0,
                'payment_method' => null,
                'amount_formatted' => null,
                'notes' => $item->notes,
            ]);
        }

        $dailyPassLogs = collect();
        if (! $typeFilter || $typeFilter === 'daily_pass') {
            $dailyPassQuery = DailyGuest::query()->latest();

            if ($dateFrom) {
                $dailyPassQuery->where('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $dailyPassQuery->where('created_at', '<=', $dateTo);
            }

            $dailyPassLogs = $dailyPassQuery->get()->map(fn (DailyGuest $item) => [
                'id' => 'guest-' . $item->id,
                'type' => 'daily_pass',
                'nama' => $item->full_name,
                'sub' => $item->phone ?: '',
                'info' => 'Daily Pass',
                'waktu' => $item->created_at?->translatedFormat('d M Y, H:i') ?? '-',
                'waktu_raw' => $item->created_at?->timestamp ?? 0,
                'payment_method' => strtoupper((string) $item->payment_method),
                'amount_formatted' => RouteHelpers::formatCurrency($item->payment_amount),
                'notes' => null,
            ]);
        }

        $merged = $memberLogs->concat($dailyPassLogs)->sortByDesc('waktu_raw')->values();
        $perPage = 10;
        $currentPage = max(1, (int) $request->input('page', 1));

        $allLogs = new LengthAwarePaginator(
            $merged->forPage($currentPage, $perPage)->values(),
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $memberOptions = GymMember::query()
            ->where('status', 'member')
            ->whereDate('expires_at', '>=', Carbon::today())
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'checkin_code']);

        return Inertia::render('Admin/Checkins', [
            'pageTitle' => 'Check-in - Arena Gym',
            'activePage' => 'checkins',
            'todayCheckinsCount' => $todayCheckinsCount,
            'todayDailyPassCount' => $todayDailyPassCount,
            'todayDailyPassRevenue' => RouteHelpers::formatCurrency($todayDailyPassRevenue),
            'allLogs' => $allLogs,
            'memberOptions' => $memberOptions,
            'paymentMethods' => ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'],
            'filters' => [
                'date_from' => $request->query('date_from', ''),
                'date_to' => $request->query('date_to', ''),
                'type' => $typeFilter ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return RouteHelpers::storeMemberCheckin(
            request: $request,
            actor: 'admin',
            redirectRoute: 'admin.checkins'
        );
    }

    public function storeDailyPass(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string'],
        ]);

        DailyGuest::query()->create([
            'full_name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'payment_amount' => (int) $validated['price'],
            'payment_method' => $validated['payment_method'],
            'visit_at' => now(),
        ]);

        return redirect()->back()->with('status', 'Daily pass berhasil dicatat!');
    }
}

