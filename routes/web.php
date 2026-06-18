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
    Route::get('list/{departemen?}', [MainPhone::class, 'index'])->name('phone.listPhone')->where('departemen', '[A-Za-z_]+');
    Route::get('detail/{id}', [DetailPhone::class, 'index'])->name('phone.detailPhone');
});

Route::prefix('user')->group(function () {
    Route::get('NotRegisterDevice', [DeviceManagement::class, 'index'])->name('user.deviceNotRegister');
    Route::post('check-device-token', [DeviceManagement::class, 'checkDeviceToken'])->name('user.checkDeviceToken');
    Route::get('registerDevice/Qr', [DeviceManagement::class, 'registerViaQR'])->name('user.deviceRegisterQR');

    Route::post('registerDevice/verify', [DeviceManagement::class, 'verifyDevice'])->name('user.deviceVerify');
    Route::post('registerDevice/confirm', [DeviceManagement::class, 'registerDevice'])->name('user.deviceRegister');
    Route::get('registerDevice/manual', [DeviceManagement::class, 'registerManual'])->name('user.deviceRegisterManual');
    Route::post('registerDevice/manual', [DeviceManagement::class, 'storeManualDevice'])->name('user.deviceRegisterManual.store');

    Route::prefix('absence/{device_id}')->group(function () {
        Route::get('/', [LoginController::class, 'index'])->name('user.loginMember');
        Route::post('/', [LoginController::class, 'store'])->name('user.loginMember.store');
        Route::get('input', [LoginController::class, 'inputForm'])->name('user.loginMember.input');
    });
    Route::prefix('dashboard/{device_id}')->group(function () {
        Route::get('/', [LoginController::class, 'dashboard'])->name('user.dashboard');
        Route::post('catatan', [LoginController::class, 'updateCatatan'])->name('user.dashboard.catatan');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('loginAdmin')->middleware('admin.guest');
    Route::post('login', [App\Http\Controllers\Admin\LoginController::class, 'authenticate'])->name('loginAdmin.authenticate');
    Route::post('logout', [App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('check-device', [CheckDeviceController::class, 'index'])->name('checkDevice');
        Route::post('check-device/verify', [CheckDeviceController::class, 'verifyToken'])->name('checkDevice.verify');

        Route::get('list-device', [ListDeviceController::class, 'index'])->name('listDevice');
        Route::post('list-device', [ListDeviceController::class, 'store']);
        Route::put('list-device/{id}', [ListDeviceController::class, 'updateBrand'])->name('listDevice.update');
        Route::delete('list-device/{id}', [ListDeviceController::class, 'destroyBrand'])->name('listDevice.destroy');

        Route::get('list-device/phone', [ListDeviceController::class, 'index']);
        Route::post('list-device/phone', [ListDeviceController::class, 'storePhone'])->name('listDevice.phone.store');
        Route::put('list-device/phone/{id}', [ListDeviceController::class, 'updatePhone'])->name('listDevice.phone.update');
        Route::delete('list-device/phone/{id}', [ListDeviceController::class, 'destroyPhone'])->name('listDevice.phone.destroy');
        Route::delete('list-device/phone/{id}/photo', [ListDeviceController::class, 'destroyPhonePhoto'])->name('listDevice.phone.photo.destroy');
        Route::delete('list-device/phone/{id}/thumbnail', [ListDeviceController::class, 'destroyPhoneThumbnail'])->name('listDevice.phone.thumbnail.destroy');
        Route::post('list-device/phone/{phone}/approve', [ListDeviceController::class, 'approvePhone'])->name('listDevice.phone.approve');

        Route::post('list-device/departemen', [ListDeviceController::class, 'storeDepartemen'])->name('listDevice.departemen.store');
        Route::put('list-device/departemen/{id}', [ListDeviceController::class, 'updateDepartemen'])->name('listDevice.departemen.update');
        Route::delete('list-device/departemen/{id}', [ListDeviceController::class, 'destroyDepartemen'])->name('listDevice.departemen.destroy');

        Route::get('admin-list', [AdminListController::class, 'index'])->name('adminList');
        Route::post('admin-list', [AdminListController::class, 'store']);
        Route::put('admin-list/{id}', [AdminListController::class, 'update'])->name('adminList.update');
        Route::delete('admin-list/{id}', [AdminListController::class, 'destroy'])->name('adminList.destroy');

        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    });
});
