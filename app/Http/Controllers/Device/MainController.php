<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Helper\JwtManager;
use App\Models\DeviceManagement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function RegisterDevice(Request $request)
    {
        $device_name = $request->input('deviceName');
        $device_id = $request->input('deviceId');

        // Check if the device is already registered
        $existingDevice = DeviceManagement::where('device_id', $device_id)->first();
        if ($existingDevice) {
            return response()->json([
                'errors' => [
                    'deviceId' => ['Device is already registered.'],
                ],
            ], 422);
        }
        //
        $jwt = new JwtManager($device_name, $device_id);

        // Create a new device record
        $device = DeviceManagement::create([
            'device_name' => $device_name,
            'device_id' => $device_id,
            'approved' => false, // Set to false until approved by admin
            'token' => $jwt->hashJwt(), // Generate a token (hashed)
        ]);

        return response()->json(['message' => 'Device registered successfully. Awaiting approval.', 'jwt' => $jwt->encode()], 201);
        //        return response()->json(['message' => 'Device registered successfully. Awaiting approval.','jwt'=>null], 201);
    }

    public function LoginDevice(string $deviceId)
    {
        $device = DeviceManagement::where('device_id', $deviceId)->first();

        if (! $device) {
            return redirect()->route('home')->with('error', 'Device not found.');
        }

        // Update last seen timestamp
        $device->last_seen_at = now();
        $device->save();

        return Inertia::render('Account/LoginPage', [
            'deviceName' => $device->device_name,
            'deviceId' => $device->device_id,
            'status' => $device->approved,
        ]);
    }

    public function GetDeviceInfo(Request $request)
    {
        $deviceId = $request->input('deviceId');
        $device = DeviceManagement::where('device_id', $deviceId)->first();

        if (! $device) {
            return response()->json(['error' => 'Device not found.'], 404);
        }

        return response()->json([
            'deviceName' => $device->device_name,
            'approved' => $device->approved,
            'lastSeenAt' => $device->last_seen_at,
        ]);
    }

    public function GetDeviceId(Request $request)
    {
        $deviceId = $request->input('deviceId');
        $device = DeviceManagement::where('device_id', $deviceId)->first();

        if (! $device) {
            return response()->json(['error' => 'Device not found.']);
        }

        return response()->json(['deviceId' => $device->device_id, 'deviceName' => $device->device_name, 'id' => $device->id, 'approved' => $device->approved]);
    }
}
