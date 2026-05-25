<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Brand;
use App\Models\PhoneList;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'totalDevices' => PhoneList::withTrashed()->count(),
            'totalBrands' => Brand::count(),
            'activeToday' => Absence::whereDate('time_absence', today())
                ->distinct('device_id')
                ->count('device_id'),
            'totalRegistered' => PhoneList::where('registered', true)->count(),
            'checkedThisMonth' => 0,
            'maintenanceCount' => 0,
            'totalUsers' => User::count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
