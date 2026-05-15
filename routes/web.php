<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Device\MainController;
use App\Http\Middleware\Admin\HasLoggedInAdmin;
use App\Http\Middleware\Admin\redirectIfHadLoggedIn;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('device/NotRegister');
})->name('home');

Route::prefix('device')->group(function () {
    Route::get('register', function () {
        return Inertia::render('device/Register');
    })->name('deviceRegister');
    Route::get('getverify', function () {
        return Inertia::render('device/VerifyDevice');
    })->name('deviceVerify');

    Route::post('add', [MainController::class, 'RegisterDevice'])->name('deviceRegisterAdd');
    Route::middleware([\App\Http\Middleware\User\ChangingSessionTimeConfigUser::class])->group(function () {
        Route::get('login/{deviceId}', [MainController::class, 'LoginDevice'])->name('deviceLogin');
    });
});

Route::prefix('admin')->middleware([\App\Http\Middleware\Admin\ChangingSessionTimeConfig::class])->group(function () {
    Route::get('/login', [AuthController::class, 'loginAdminPanel'])->name('admin.loginAdmin')->middleware([redirectIfHadLoggedIn::class]);
    Route::post('/login', [AuthController::class, 'loginAdmin'])->name('admin.loginPost');
    Route::middleware([HasLoggedInAdmin::class])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');
        Route::get('verify', function () {
            return Inertia::render('Admin/VerifyDevices');
        })->name('admin.verifyDevice');
        Route::get('/logout', [AuthController::class, 'logoutAdmin'])->name('admin.logoutGet');
    });
});
