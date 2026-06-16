<?php

use App\Helpers\RouteHelpers;
use App\Models\DailyGuest;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin - Check-in Hub (Member & Daily Pass)
|--------------------------------------------------------------------------
*/

Route::get('/checkins', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $startOfToday = now()->startOfDay();
    $endOfToday = now()->endOfDay();

    $todayCheckinsCount = GymCheckin::where('verification_status', 'verified')
        ->whereBetween('checked_in_at', [$startOfToday, $endOfToday])
        ->count();

    $todayDailyPassCount = DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->count();

    $todayDailyPassRevenue = DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
        ->sum('payment_amount');

    $dateFrom = $request->filled('date_from')
        ? Carbon::parse($request->date_from)->startOfDay()
        : null;

    $dateTo = $request->filled('date_to')
        ? Carbon::parse($request->date_to)->endOfDay()
        : null;

    $typeFilter = $request->input('type'); // member | daily_pass | null

    $memberLogs = collect();
    if (!$typeFilter || $typeFilter === 'member') {
        $memberQuery = GymCheckin::with('member')
            ->where('verification_status', 'verified')
            ->latest('checked_in_at');

        if ($dateFrom) {
            $memberQuery->where('checked_in_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $memberQuery->where('checked_in_at', '<=', $dateTo);
        }

        $memberLogs = $memberQuery->get()->map(fn ($item) => [
            'type' => 'member',
            'nama' => $item->member->full_name ?? 'N/A',
            'sub' => $item->member->checkin_code ?? '',
            'info' => 'Aktif hingga: ' . ($item->member->expires_at?->format('d M Y') ?? '-'),
            'waktu' => $item->checked_in_at->translatedFormat('d M Y, H:i'),
            'waktu_raw' => $item->checked_in_at,
            'payment_method' => null,
            'amount' => null,
        ]);
    }

    $dailyPassLogs = collect();
    if (!$typeFilter || $typeFilter === 'daily_pass') {
        $dailyPassQuery = DailyGuest::latest();

        if ($dateFrom) {
            $dailyPassQuery->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $dailyPassQuery->where('created_at', '<=', $dateTo);
        }

        $dailyPassLogs = $dailyPassQuery->get()->map(fn ($item) => [
            'type' => 'daily_pass',
            'nama' => $item->full_name,
            'sub' => '',
            'info' => 'Daily Pass',
            'waktu' => $item->created_at->translatedFormat('d M Y, H:i'),
            'waktu_raw' => $item->created_at,
            'payment_method' => $item->payment_method,
            'amount' => $item->payment_amount,
        ]);
    }

    $merged = $memberLogs->concat($dailyPassLogs)->sortByDesc('waktu_raw')->values();
    $perPage = 10;
    $currentPage = (int) $request->input('page', 1);

    $allLogs = new \Illuminate\Pagination\LengthAwarePaginator(
        $merged->forPage($currentPage, $perPage),
        $merged->count(),
        $perPage,
        $currentPage,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    $memberOptions = GymMember::where('status', 'member')
        ->where('expires_at', '>=', now())
        ->orderBy('full_name')
        ->get(['id', 'full_name', 'checkin_code']);

    $paymentMethods = ['Cash', 'Transfer Bank', 'QRIS', 'Debit Card'];

    return view('admin.checkins', array_merge(RouteHelpers::pageMeta('checkins'), [
        'todayCheckinsCount' => $todayCheckinsCount,
        'todayDailyPassCount' => $todayDailyPassCount,
        'todayDailyPassRevenue' => $todayDailyPassRevenue,
        'allLogs' => $allLogs,
        'memberOptions' => $memberOptions,
        'paymentMethods' => $paymentMethods,
        'checkinRecords' => GymCheckin::with('member')
            ->where('verification_status', 'verified')
            ->whereBetween('checked_in_at', [$startOfToday, $endOfToday])
            ->latest('checked_in_at')
            ->get(),
        'dailyPassEntries' => DailyGuest::whereBetween('created_at', [$startOfToday, $endOfToday])
            ->latest()
            ->get(),
    ]));
})->name('checkins');

Route::post('/checkins', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    return RouteHelpers::storeMemberCheckin(
        request: $request,
        actor: 'admin',
        redirectRoute: 'admin.checkins'
    );
})->name('checkins.store');

Route::post('/checkins/daily-pass', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'price' => 'required|numeric',
        'payment_method' => 'required|string',
    ]);

    DailyGuest::create([
        'full_name' => $validated['name'],
        'phone' => $validated['phone'] ?? null,
        'payment_amount' => $validated['price'],
        'payment_method' => $validated['payment_method'],
        'visit_at' => now(),
    ]);

    return redirect()->back()->with('status', 'Daily pass berhasil dicatat!');
})->name('checkins.daily-pass.store');
