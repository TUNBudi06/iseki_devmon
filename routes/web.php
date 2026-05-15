<?php

use App\Http\Controllers\Admin\VerifyDeviceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Device\MainController;
use App\Http\Middleware\Admin\ChangingSessionTimeConfig;
use App\Http\Middleware\Admin\HasLoggedInAdmin;
use App\Http\Middleware\Admin\redirectIfHadLoggedIn;
use App\Http\Middleware\User\ChangingSessionTimeConfigUser;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home route
Route::get('/', fn () => Inertia::render('device/NotRegister'))->name('home');
