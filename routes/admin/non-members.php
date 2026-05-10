<?php

use App\Models\DailyGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\RouteHelpers;

/*
|--------------------------------------------------------------------------
| Admin – Manajemen Non-Member (Tamu Harian)
|--------------------------------------------------------------------------
*/

Route::get('/non-members', function (Request $request) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $search = trim($request->string('q')->value());

    // PERBAIKAN: Ambil dari model DailyGuest, bukan GymMember
    $query = DailyGuest::query()->latest('visit_at');

    if ($search !== '') {
        $query->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%');
    }

    $guests = $query->paginate(10);

    return view('admin.non_members', array_merge(RouteHelpers::pageMeta('non-members'), [
        'guests' => $guests,
        'search' => $search,
    ]));
})->name('non-members');

// Route untuk menghapus riwayat kunjungan tamu
Route::delete('/non-members/{guest}', function (DailyGuest $guest) {
    if ($redirect = RouteHelpers::ensureAdmin()) {
        return $redirect;
    }

    $guest->delete();

    return redirect()->route('admin.non-members')->with('status', 'Data kunjungan berhasil dihapus.');
})->name('non-members.destroy');
