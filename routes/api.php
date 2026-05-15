<?php

use App\Http\Controllers\Device\MainController;

Route::prefix('v1')->group(function () {
    Route::prefix('device')->group(function () {
        Route::post('info', [MainController::class, 'GetDeviceInfo'])->name('api.deviceInfo');
        Route::post('getId', [MainController::class, 'GetDeviceId'])->name('api.deviceInfoId');
    });

});
