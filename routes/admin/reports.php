<?php

use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::get('/reports/{reportSlug}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/export/member-data', [ReportController::class, 'exportMemberData'])->name('export.member-data');
Route::post('/reports/expenses', [ReportController::class, 'storeExpense'])->name('reports.expenses.store');
