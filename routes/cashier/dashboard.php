<?php

use App\Helpers\RouteHelpers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cashier – Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if ($redirect = RouteHelpers::ensureCashier()) {
        return $redirect;
    }

    return view('cashier.dashboard_home', RouteHelpers::buildCashierViewData());
})->name('dashboard');
