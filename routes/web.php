<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('device/NotRegister');
})->name('home');


Route::prefix('device')->group(function () {
    Route::get('register', function () {
        return Inertia::render('device/Register');
    })->name('deviceRegister');

    Route::post('add',[\App\Http\Controllers\Device\MainController::class,'RegisterDevice'])->name('deviceRegisterAdd');

    Route::get('login/{deviceId}',[\App\Http\Controllers\Device\MainController::class,'LoginDevice'])->name('deviceLogin');
});
