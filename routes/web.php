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

// Device routes
Route::prefix('device')->group(function () {
    Route::get('register', fn () => Inertia::render('device/Register'))->name('deviceRegister');
    Route::get('getverify', fn () => Inertia::render('device/VerifyDevice'))->name('deviceVerify');
    Route::post('add', [MainController::class, 'RegisterDevice'])->name('deviceRegisterAdd');

    Route::middleware([ChangingSessionTimeConfigUser::class])->group(function () {
        Route::get('login/{deviceId}', [MainController::class, 'LoginDevice'])->name('deviceLogin');
    });
});

// Admin routes with session config middleware
Route::prefix('admin')->middleware([ChangingSessionTimeConfig::class])->group(function () {
    // Auth routes
    Route::get('login', [AuthController::class, 'loginAdminPanel'])->name('admin.loginAdmin')->middleware([redirectIfHadLoggedIn::class]);
    Route::post('login', [AuthController::class, 'loginAdmin'])->name('admin.loginPost');

    // Protected admin routes
    Route::middleware([HasLoggedInAdmin::class])->group(function () {
        Route::get('dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
        Route::get('verify', fn () => Inertia::render('Admin/VerifyDevices'))->name('admin.verifyDevice');
        Route::post('verify', [VerifyDeviceController::class, 'verifyDevice'])->name('admin.verifyDevicePost');
        Route::get('logout', [AuthController::class, 'logoutAdmin'])->name('admin.logoutAdmin');
    });
});
