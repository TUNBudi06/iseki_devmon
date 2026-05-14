<?php



Route::prefix('v1')->group(function () {;
    Route::prefix('device')->group(function () {
        Route::post('info',[\App\Http\Controllers\Device\MainController::class,'GetDeviceInfo'])->name('api.deviceInfo');
        Route::post('getId',[\App\Http\Controllers\Device\MainController::class,'GetDeviceId'])->name('api.deviceInfoId');
    });

});
