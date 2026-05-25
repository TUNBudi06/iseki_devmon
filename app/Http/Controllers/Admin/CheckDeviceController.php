<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckDeviceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/CheckDevice');
    }

    public function verifyToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $token = $validated['token'];

        // Cari berdasarkan hash_token atau model_id
        $device = PhoneList::where('hash_token', $token)
            ->orWhere('model_id', $token)
            ->with('brand')
            ->first();

        if (! $device) {
            return response()->json([
                'valid' => false,
                'message' => 'Token tidak cocok dengan perangkat manapun.',
            ], 404);
        }

        // Verifikasi hash_token cocok
        if ($device->hash_token !== $token && $device->model_id !== $token) {
            return response()->json([
                'valid' => false,
                'message' => 'Token tidak valid untuk perangkat ini.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Perangkat terverifikasi!',
            'device' => [
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
                'model_type' => $device->model_type,
                'brand_name' => $device->brand?->name,
                'registered' => $device->registered,
                'approved' => $device->approved,
                'ram' => $device->ram,
                'storage' => $device->storage,
                'thumbnail' => $device->thumbnail,
                'hash_token' => $device->hash_token,
            ],
        ]);
    }
}
