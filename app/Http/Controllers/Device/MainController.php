<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Helper\JwtManager;
use App\Models\DeviceManagement;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function RegisterDevice(Request $request)
    {
        $data = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255|unique:device_managements,device_id'
        ]);
        debugbar()->info($data);
//        // Check if the device is already registered
//        $existingDevice = \App\Models\DeviceManagement::where('device_id', $deviceId)->first();
//        if ($existingDevice) {
//            return response()->json(['error' => 'Device is already registered.'], 400);
//        }
//
//        $jwt = new JwtManager($deviceName, $deviceId);

//        // Create a new device record
//        $device = \App\Models\DeviceManagement::create([
//            'device_name' => $deviceName,
//            'device_id' => $deviceId,
//            'approved' => false, // Set to false until approved by admin
//            'token' => $jwt->hashJwt(), // Generate a token (hashed)
//        ]);

//        return response()->json(['message' => 'Device registered successfully. Awaiting approval.','jwt'=>$jwt->encode()], 201);
        return response()->json(['message' => 'Device registered successfully. Awaiting approval.','jwt'=>null], 201);
    }
}
