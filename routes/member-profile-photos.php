<?php

use Illuminate\Support\Facades\Route;

Route::get('/member-profile-photos/{path}', function (string $path) {
    $relativePath = str_replace(['..', '\\'], '', $path);
    $userStoragePath = base_path('user/storage/app/public/' . $relativePath);
    $rootStoragePath = storage_path('app/public/' . $relativePath);

    if (is_file($userStoragePath)) {
        return response()->file($userStoragePath);
    }

    abort_unless(is_file($rootStoragePath), 404);

    return response()->file($rootStoragePath);
})->where('path', '.*')->name('member.profile-photo.show');
