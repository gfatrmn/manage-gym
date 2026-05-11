<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Check-in Hub (Member & Daily Guest)
|--------------------------------------------------------------------------
*/

// ── Index (Updated: Menampilkan Semua Riwayat dengan Pagination) ──────────────
Route::get('/checkins', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today = Carbon::today();

    // 1. Ambil SEMUA Riwayat Check-in Member (Paginasi 10 data)
    $checkinRecords = GymCheckin::query()
        ->with('member')
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->paginate(10, ['*'], 'member_page') // Menggunakan nama page khusus agar tidak bentrok
        ->withQueryString();

    // 2. Ambil SEMUA Riwayat Tamu Harian (Paginasi 10 data)
    $dailyGuests = DailyGuest::query()
        ->latest()
        ->paginate(10, ['*'], 'guest_page')
        ->withQueryString();

    // Data pendukung untuk Dashboard Check-in
    $latestCheckin = GymCheckin::query()
        ->with('member')
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->first();

    return view('admin.checkins', array_merge(RouteHelpers::pageMeta('checkins'), [
        'checkinRecords'     => $checkinRecords,
        'dailyGuests'        => $dailyGuests,
        'memberOptions'      => GymMember::query()
            ->where('status', 'active')
            ->whereDate('expires_at', '>=', $today)
            ->orderBy('full_name')
            ->get(),
        // Counter tetap menghitung data hari ini untuk informasi di widget atas
        'todayCheckinsCount' => GymCheckin::whereDate('checked_in_at', $today)->where('verification_status', 'verified')->count(),
        'todayGuestsCount'   => DailyGuest::whereDate('created_at', $today)->count(),
        'latestCheckin'      => $latestCheckin,
    ]));
})->name('checkins');

// ── Store Member Check-in ─────────────────────────────────────────────────────
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

// ── Store Daily Guest ─────────────────────────────────────────────────────────
Route::post('/checkins/guest', function (Request $request) {
    if ($redirect = \App\Helpers\RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'           => 'required|string|max:255',
        'phone'          => 'nullable|string|max:20',
        'price'          => 'required|numeric',
        'payment_method' => 'required|string',
    ]);

    DailyGuest::create([
        'full_name'      => $validated['name'],
        'phone'          => $validated['phone'] ?? null,
        'payment_amount' => $validated['price'],
        'payment_method' => $validated['payment_method'],
        'visit_at'       => now(),
    ]);

    return redirect()->back()->with('status', 'Tamu harian berhasil dicatat!');
})->name('checkins.guest.store');
