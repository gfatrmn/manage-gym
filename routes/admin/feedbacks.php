<?php

use App\Helpers\RouteHelpers;
use App\Models\MemberFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/feedbacks', function () {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $feedbacks = MemberFeedback::query()
        ->latest()
        ->paginate(15);

    $unreadCount = MemberFeedback::query()->whereNull('read_at')->count();

    return view('admin.feedbacks', array_merge(RouteHelpers::pageMeta('dashboard'), [
        'pageTitle' => 'Kritik & Saran - Arena Gym',
        'activePage' => 'feedbacks',
        'feedbacks' => $feedbacks,
        'unreadCount' => $unreadCount,
    ]));
})->name('feedbacks');

Route::post('/feedbacks/{feedback}/read', function (Request $request, MemberFeedback $feedback) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;
    $feedback->update(['read_at' => now()]);

    if ($request->expectsJson()) {
        return response()->json(['ok' => true]);
    }

    return back()->with('status', 'Pesan ditandai sudah dibaca.');
})->name('feedbacks.read');

Route::delete('/feedbacks/{feedback}', function (MemberFeedback $feedback) {
    if ($redirect = RouteHelpers::ensureAdmin()) return $redirect;

    $feedback->delete();

    return back()->with('status', 'Kritik dan saran berhasil dihapus.');
})->name('feedbacks.destroy');
