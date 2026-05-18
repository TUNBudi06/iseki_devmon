<?php

use App\Http\Controllers\Admin\VerifyDeviceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Device\MainController;
use App\Http\Controllers\Phone\MainPhone;
use App\Http\Middleware\Admin\ChangingSessionTimeConfig;
use App\Http\Middleware\Admin\HasLoggedInAdmin;
use App\Http\Middleware\Admin\redirectIfHadLoggedIn;
use App\Http\Middleware\User\ChangingSessionTimeConfigUser;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home route
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::prefix('phone')->group(function () {
   Route::get('list',[MainPhone::class,'index'])->name('phone.listPhone');
   Route::get('detail/{id}',[\App\Http\Controllers\Phone\DetailPhone::class,'index'])->name('phone.detailPhone');
});
