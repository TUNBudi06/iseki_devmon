<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneList;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Maintenance');
    }

    public function getDeviceByQr(Request $request){
        $validated = $request->validate([
           'model_id' => 'string|required'
        ]);

        $device = PhoneList::where('model_id',$validated['model_id'])->first();
        if(!$device) \response()->json(['errors.model_id'=>'Perangkat tidak ditemukan!']);

        return \response()->json($device);
    }
}
