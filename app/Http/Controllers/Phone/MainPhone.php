<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Departemen;
use App\Models\PhoneList;
use Inertia\Inertia;

class MainPhone extends Controller
{
    public function index()
    {
        return Inertia::render('Phone/Dashboard', [
            'devices' => fn () => PhoneList::with('brand')
                ->where('approved', true)
                ->orderBy('created_at', 'desc')
                ->get(),
            'latestAbsences' => fn () => Absence::selectRaw('device_id, name as latest_user, nik as latest_user_nik, time_absence as latest_time')
                ->whereIn('device_id', fn ($q) => $q->select('model_id')->from('phone_lists')->where('approved', true))
                ->whereIn('id', fn ($query) => $query->selectRaw('MAX(id)')->from('absences')->groupBy('device_id'))
                ->get()
                ->keyBy('device_id'),
            'todayActiveDeviceIds' => fn () => Absence::whereDate('time_absence', today())
                ->whereIn('device_id', fn ($q) => $q->select('model_id')->from('phone_lists')->where('approved', true))
                ->pluck('device_id')
                ->unique()
                ->values(),
            'departemenOptions' => fn () => Departemen::orderBy('id')->get(),
        ]);
    }
}
