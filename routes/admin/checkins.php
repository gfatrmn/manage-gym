<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use App\Models\DailyGuest; // Pastikan model DailyGuest di-import
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Check-in Hub (Member & Daily Guest)
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/checkins', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today = Carbon::today();

    // 1. Ambil Check-in Member hari ini
    $todayCheckins = GymCheckin::query()
        ->with('member')
        ->whereDate('checked_in_at', $today)
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->get();

    // 2. Ambil Tamu Harian (Daily Guest) hari ini
    $dailyGuests = DailyGuest::query()
        ->whereDate('created_at', $today)
        ->latest()
        ->get();

    $latestCheckin = GymCheckin::query()
        ->with('member')
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->first();

    return view('admin.checkins', array_merge(RouteHelpers::pageMeta('checkins'), [
        'checkinRecords'     => $todayCheckins,
        'dailyGuests'        => $dailyGuests, // Tambahkan data tamu harian
        'memberOptions'      => GymMember::query()
            ->where('status', 'active') // Sesuaikan dengan kolom status kamu
            ->whereDate('expires_at', '>=', $today)
            ->orderBy('full_name')
            ->get(),
        'todayCheckinsCount' => $todayCheckins->count(),
        'todayGuestsCount'   => $dailyGuests->count(), // Hitung jumlah tamu harian
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

// ── Store Daily Guest (Pindahan dari Non-Member) ──────────────────────────────
Route::post('/checkins/guest', function (Request $request) {
    if ($redirect = \App\Helpers\RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'name'           => 'required|string|max:255',
        'phone'          => 'nullable|string|max:20',
        'price'          => 'required|numeric', // Input dari form
        'payment_method' => 'required|string',
    ]);

    \App\Models\DailyGuest::create([
        'full_name'      => $validated['name'],
        'phone'          => $validated['phone'] ?? null,
        'payment_amount' => $validated['price'], // SESUAIKAN DENGAN DATABASE
        'payment_method' => $validated['payment_method'],
        'visit_at'       => now(),
    ]);

    return redirect()->back()->with('status', 'Tamu harian berhasil dicatat!');
})->name('checkins.guest.store');
