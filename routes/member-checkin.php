<?php

use App\Helpers\RouteHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member Self-Service Check-in (QR)
|--------------------------------------------------------------------------
| Halaman ini diakses member melalui QR code yang dipasang di gym.
| Setelah submit, check-in masuk sebagai "pending" dan menunggu
| validasi kasir.
*/

Route::get('/member-checkin', function () {
    return view('checkin.member-self-service', [
        'pageTitle' => 'Check-in Member Arena Gym',
    ]);
})->name('member.checkin');

Route::post('/member-checkin', function (Request $request) {
    return RouteHelpers::storeMemberCheckin(
        request: $request,
        actor: 'qr_member',
        redirectRoute: 'member.checkin'
    );
})->name('member.checkin.store');
