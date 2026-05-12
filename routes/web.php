<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Account/LoginPage');
})->name('loginUser');


Route::prefix('device')->group(function () {
    Route::get('not-register', function () {
        return Inertia::render('device/NotRegister');
    })->name('deviceNotRegister');

    Route::get('register', function () {
        return Inertia::render('device/Register');
    })->name('deviceRegister');

    Route::post('add',[\App\Http\Controllers\Device\MainController::class,'RegisterDevice'])->name('deviceRegisterAdd');
});
