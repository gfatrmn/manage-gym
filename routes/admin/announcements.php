<?php

use App\Helpers\RouteHelpers;
use App\Models\Announcement;
use App\Models\GymMember;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin – Pengumuman & Pengingat Membership
|--------------------------------------------------------------------------
*/

// ── Index ─────────────────────────────────────────────────────────────────────
Route::get('/announcements', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $today            = Carbon::today();
    $sevenDaysFromNow = $today->copy()->addDays(7);

    $announcements = Announcement::query()
        ->latest('publish_at')
        ->latest()
        ->get();

    $expiringMembers = GymMember::query()
        ->where('member_status', 'member')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '>=', $today)
        ->whereDate('expires_at', '<=', $sevenDaysFromNow)
        ->orderBy('expires_at')
        ->orderBy('full_name')
        ->get();

    return view('admin.announcements', array_merge(RouteHelpers::pageMeta('announcements'), [
        'announcements'  => $announcements,
        'expiringMembers'=> $expiringMembers,
    ]));
})->name('announcements');

// ── Publish langsung ──────────────────────────────────────────────────────────
Route::post('/announcements/publish', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'body'  => ['required', 'string'],
    ]);

    Announcement::create([
        'title'      => $validated['title'],
        'body'       => $validated['body'],
        'status'     => 'active',
        'publish_at' => now(),
    ]);

    return back()->with('status', 'Pengumuman baru berhasil dipublikasikan.');
})->name('announcements.publish');

// ── Jadwalkan ─────────────────────────────────────────────────────────────────
Route::post('/announcements/schedule', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'title'      => ['required', 'string', 'max:255'],
        'body'       => ['required', 'string'],
        'publish_at' => ['required', 'date'],
    ]);

    Announcement::create([
        'title'      => $validated['title'],
        'body'       => $validated['body'],
        'status'     => 'scheduled',
        'publish_at' => Carbon::parse($validated['publish_at']),
    ]);

    return back()->with('status', 'Pengumuman berhasil dijadwalkan.');
})->name('announcements.schedule');

// ── Arsipkan ──────────────────────────────────────────────────────────────────
Route::post('/announcements/archive', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'announcement_id' => ['required', 'exists:announcements,id'],
    ]);

    Announcement::query()
        ->whereKey($validated['announcement_id'])
        ->update(['status' => 'archived', 'archived_at' => now()]);

    return back()->with('status', 'Pengumuman berhasil dipindahkan ke arsip.');
})->name('announcements.archive');

// ── Kirim pengingat perpanjangan ke member ────────────────────────────────────
Route::post('/announcements/reminders', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'gym_member_id' => ['required', 'exists:gym_members,id'],
    ]);

    $member = GymMember::query()->findOrFail($validated['gym_member_id']);
    abort_unless($member->member_status === 'member', 404);

    $member->update(['last_membership_reminder_at' => now()]);

    return back()->with('status', "Pengingat perpanjangan untuk {$member->full_name} berhasil dikirim.");
})->name('announcements.reminders.send');
