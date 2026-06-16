<?php

use App\Models\DailyGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\RouteHelpers;

/*
|--------------------------------------------------------------------------
| Admin - Manajemen Daily Pass (Tamu Harian)
|--------------------------------------------------------------------------
*/

Route::get('/daily-passes', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $search = trim($request->string('q')->value());

    $query = DailyGuest::query()->latest('visit_at');

    if ($search !== '') {
        $query->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%');
    }

    $dailyPasses = $query->paginate(10);

    return view('admin.daily_passes', array_merge(RouteHelpers::pageMeta('daily-passes'), [
        'dailyPasses' => $dailyPasses,
        'search' => $search,
    ]));
})->name('daily-passes');

Route::post('/daily-passes', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $validated = $request->validate([
        'full_name'      => ['required', 'string', 'max:255'],
        'phone'          => ['nullable', 'string', 'max:30'],
        'payment_method' => ['required', 'string', 'max:30'],
        'payment_amount' => ['nullable', 'integer', 'min:1'],
        'notes'          => ['nullable', 'string'],
    ]);

    DailyGuest::query()->create([
        'full_name'      => $validated['full_name'],
        'phone'          => $validated['phone'] ?? null,
        'payment_amount' => (int) ($validated['payment_amount'] ?? 30000),
        'payment_method' => $validated['payment_method'],
        'visit_at'       => now(),
    ]);

    return redirect()->route('admin.daily-passes')->with('status', 'Data kunjungan daily pass berhasil ditambahkan.');
})->name('daily-passes.store');

Route::delete('/daily-passes/{dailyPass}', function (DailyGuest $dailyPass) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $dailyPass->delete();

    return redirect()->route('admin.daily-passes')->with('status', 'Data kunjungan daily pass berhasil dihapus.');
})->name('daily-passes.destroy');

