<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'totalDevices' => DB::table('phone_lists')->count(),
            'totalBrands' => DB::table('brands')->count(),
            'activeToday' => DB::table('phone_lists')->whereDate('created_at', today())->count(),
            'totalRegistered' => DB::table('phone_lists')->where('registered', true)->count(),
            'checkedThisMonth' => 0,
            'maintenanceCount' => 0,
            'totalUsers' => User::count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
