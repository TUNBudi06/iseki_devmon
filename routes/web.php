<?php

use App\Http\Controllers\Admin\AdminListController;
use App\Http\Controllers\Admin\CheckDeviceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ListDeviceController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Phone\DetailPhone;
use App\Http\Controllers\Phone\MainPhone;
use App\Http\Controllers\User\DeviceManagement;
use App\Http\Controllers\User\LoginController as UserLoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Home ─────────────────────────────────────────────────────
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

// ─── Public: Phone List & Detail ──────────────────────────────
Route::prefix('device')->name('phone.')->group(function () {
    Route::get('list/{departemen?}', [MainPhone::class, 'index'])
        ->name('listPhone')
        ->where('departemen', '[A-Za-z_]+');

    Route::get('detail/{id}', [DetailPhone::class, 'index'])
        ->name('detailPhone');
});

// ─── User: Registration & Absence ─────────────────────────────
Route::prefix('user')->name('user.')->group(function () {
    Route::get('NotRegisterDevice', [DeviceManagement::class, 'index'])->name('deviceNotRegister');
    Route::post('check-device-token', [DeviceManagement::class, 'checkDeviceToken'])->name('checkDeviceToken');
    Route::get('registerDevice/Qr', [DeviceManagement::class, 'registerViaQR'])->name('deviceRegisterQR');

    Route::post('registerDevice/verify', [DeviceManagement::class, 'verifyDevice'])->name('deviceVerify');
    Route::post('registerDevice/confirm', [DeviceManagement::class, 'registerDevice'])->name('deviceRegister');
    Route::get('registerDevice/manual', [DeviceManagement::class, 'registerManual'])->name('deviceRegisterManual');
    Route::post('registerDevice/manual', [DeviceManagement::class, 'storeManualDevice'])->name('deviceRegisterManual.store');

    Route::prefix('absence/{device_id}')->group(function () {
        Route::get('/', [UserLoginController::class, 'index'])->name('user.loginMember');
        Route::post('/', [UserLoginController::class, 'store'])->name('user.loginMember.store');
        Route::get('input', [UserLoginController::class, 'inputForm'])->name('user.loginMember.input');
    });

    Route::prefix('dashboard/{device_id}')->group(function () {
        Route::get('/', [UserLoginController::class, 'dashboard'])->name('user.dashboard');
        Route::post('catatan', [UserLoginController::class, 'updateCatatan'])->name('user.dashboard.catatan');
    });
});

// ─── Admin Panel ──────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only
    Route::middleware('admin.guest')->group(function () {
        Route::get('login', [AdminLoginController::class, 'index'])->name('loginAdmin');
        Route::post('login', [AdminLoginController::class, 'authenticate'])->name('loginAdmin.authenticate');
    });

    // Logout (no middleware — works even if session expired)
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Authenticated
    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Check Device ──
        Route::get('check-device', [CheckDeviceController::class, 'index'])->name('checkDevice');
        Route::post('check-device/verify', [CheckDeviceController::class, 'verifyToken'])->name('checkDevice.verify');

        // ── Device List ──
        Route::get('list-device', [ListDeviceController::class, 'index'])->name('listDevice');
        Route::post('list-device', [ListDeviceController::class, 'store']);
        Route::put('list-device/{id}', [ListDeviceController::class, 'updateBrand'])->name('listDevice.update');
        Route::delete('list-device/{id}', [ListDeviceController::class, 'destroyBrand'])->name('listDevice.destroy');

        // ── Phone CRUD ──
        Route::post('list-device/phone', [ListDeviceController::class, 'storePhone'])->name('listDevice.phone.store');
        Route::put('list-device/phone/{id}', [ListDeviceController::class, 'updatePhone'])->name('listDevice.phone.update');
        Route::delete('list-device/phone/{id}', [ListDeviceController::class, 'destroyPhone'])->name('listDevice.phone.destroy');
        Route::delete('list-device/phone/{id}/photo', [ListDeviceController::class, 'destroyPhonePhoto'])->name('listDevice.phone.photo.destroy');
        Route::post('list-device/phone/{phone}/approve', [ListDeviceController::class, 'approvePhone'])->name('listDevice.phone.approve');

        // ── Departemen CRUD ──
        Route::post('list-device/departemen', [ListDeviceController::class, 'storeDepartemen'])->name('listDevice.departemen.store');
        Route::put('list-device/departemen/{id}', [ListDeviceController::class, 'updateDepartemen'])->name('listDevice.departemen.update');
        Route::delete('list-device/departemen/{id}', [ListDeviceController::class, 'destroyDepartemen'])->name('listDevice.departemen.destroy');

        // ── Admin Users ──
        Route::get('admin-list', [AdminListController::class, 'index'])->name('adminList');
        Route::post('admin-list', [AdminListController::class, 'store']);
        Route::put('admin-list/{id}', [AdminListController::class, 'update'])->name('adminList.update');
        Route::delete('admin-list/{id}', [AdminListController::class, 'destroy'])->name('adminList.destroy');

        // Maintenance
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    });
});
