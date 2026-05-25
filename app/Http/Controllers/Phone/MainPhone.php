<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\PhoneList;
use Inertia\Inertia;

class MainPhone extends Controller
{
    public function index()
    {
        $devices = PhoneList::with('brand')
            ->where('approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil absensi terbaru per device (all-time)
        $latestAbsences = Absence::selectRaw('device_id, name as latest_user, nik as latest_user_nik, time_absence as latest_time')
            ->whereIn('device_id', $devices->pluck('model_id'))
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('absences')
                    ->groupBy('device_id');
            })
            ->get()
            ->keyBy('device_id');

        // Device yang aktif hari ini (ada absensi hari ini)
        $todayActiveDeviceIds = Absence::whereDate('time_absence', today())
            ->whereIn('device_id', $devices->pluck('model_id'))
            ->pluck('device_id')
            ->unique()
            ->values();

        return Inertia::render('Phone/Dashboard', [
            'devices' => $devices,
            'latestAbsences' => $latestAbsences,
            'todayActiveDeviceIds' => $todayActiveDeviceIds,
        ]);
    }
}
