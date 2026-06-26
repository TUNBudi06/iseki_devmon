<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Brand;
use App\Models\DeviceCheck;
use App\Models\PhoneList;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => fn () => [
                'totalDevices' => PhoneList::withTrashed()->count(),
                'totalBrands' => Brand::count(),
                'activeToday' => Absence::whereDate('time_absence', today())
                    ->distinct('device_id')
                    ->count('device_id'),
                'totalRegistered' => PhoneList::where('registered', true)->count(),
                'checkedThisMonth' => DeviceCheck::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'maintenanceCount' => DeviceCheck::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->distinct('phone_list_id')
                    ->count('phone_list_id'),
                'totalUsers' => User::count(),
            ],
        ]);
    }
}
