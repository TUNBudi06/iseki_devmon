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

        // Cari device berdasarkan hash_token
        $device = PhoneList::where('hash_token', $token)
            ->with('brand')
            ->first();

        if (! $device) {
            return response()->json([
                'valid' => false,
                'token_input' => $token,
                'matched_by' => null,
                'message' => 'Token tidak cocok dengan hash_token perangkat manapun.',
            ], 404);
        }

        // Verifikasi hash_token cocok persis
        $match = $device->hash_token === $token;

        return response()->json([
            'valid' => $match,
            'token_input' => $token,
            'matched_by' => 'hash_token',
            'message' => $match ? '✓ Hash token cocok! Perangkat terverifikasi.' : 'Token tidak sesuai dengan hash_token perangkat.',
            'device' => $match ? [
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
            ] : null,
        ]);
    }
}
