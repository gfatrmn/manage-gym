<?php

use App\Helpers\RouteHelpers;
use App\Models\GymCheckin;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Check-in
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/checkins', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today = Carbon::today();

    $todayCheckins = GymCheckin::query()
        ->with('member')
        ->whereDate('checked_in_at', $today)
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->get();

    $latestCheckin = GymCheckin::query()
        ->with('member')
        ->where('verification_status', 'verified')
        ->latest('checked_in_at')
        ->first();

    return view('admin.checkins', array_merge(RouteHelpers::pageMeta('checkins'), [
        'checkinRecords'    => $todayCheckins,
        'memberOptions'     => GymMember::query()
            ->where('member_status', 'member')
            ->whereDate('expires_at', '>=', $today)
            ->orderBy('full_name')
            ->get(),
        'todayCheckinsCount'=> $todayCheckins->count(),
        'latestCheckin'     => $latestCheckin,
    ]));
})->name('checkins');

// ── Store ─────────────────────────────────────────────────────────────────────
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
