<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyDeviceController extends Controller
{
    //
    public function verifyDevice(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:device_management,id',
            'comment' => 'nullable|string',
            'image'=>'required|min:1|array|max:4',
            'image.*'=>'required|image|mimes:image|max:5120',
        ]);

        return response()->json($data, 200);
    }
}
