<?php

use App\Http\Controllers\Admin\AdminListController;
use App\Http\Controllers\Admin\CheckDeviceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ListDeviceController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Phone\DetailPhone;
use App\Http\Controllers\Phone\MainPhone;
use App\Http\Controllers\User\DeviceManagement;
use App\Http\Controllers\User\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home route
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::prefix('device')->group(function () {
    Route::get('list', [MainPhone::class, 'index'])->name('phone.listPhone');
    Route::get('detail/{id}', [DetailPhone::class, 'index'])->name('phone.detailPhone');
});

Route::prefix('user')->group(function () {
    Route::get('NotRegisterDevice', [DeviceManagement::class, 'index'])->name('user.deviceNotRegister');
    Route::get('registerDevice/Qr', [DeviceManagement::class, 'registerViaQR'])->name('user.deviceRegisterQR');

    Route::get('absence', [LoginController::class, 'index'])->name('user.loginMember');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('loginAdmin');
    Route::post('login', [App\Http\Controllers\Admin\LoginController::class, 'authenticate'])->name('loginAdmin.authenticate');
    Route::post('logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('check-device', [CheckDeviceController::class, 'index'])->name('checkDevice');

        Route::get('list-device', [ListDeviceController::class, 'index'])->name('listDevice');
        Route::post('list-device', [ListDeviceController::class, 'store']);
        Route::put('list-device/{id}', [ListDeviceController::class, 'update'])->name('listDevice.update');
        Route::delete('list-device/{id}', [ListDeviceController::class, 'destroy'])->name('listDevice.destroy');

        Route::get('admin-list', [AdminListController::class, 'index'])->name('adminList');
        Route::post('admin-list', [AdminListController::class, 'store']);
        Route::put('admin-list/{id}', [AdminListController::class, 'update'])->name('adminList.update');
        Route::delete('admin-list/{id}', [AdminListController::class, 'destroy'])->name('adminList.destroy');

        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    });
});
