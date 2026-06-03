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

        // Cari device berdasarkan hash_token ATAU model_id
        $device = PhoneList::where('hash_token', $token)
            ->orWhere('model_id', $token)
            ->with('brand')
            ->first();

        if (! $device) {
            return response()->json([
                'valid' => false,
                'token_input' => $token,
                'matched_by' => null,
                'message' => 'Token tidak ditemukan. Pastikan kode QR berasal dari perangkat yang terdaftar.',
            ], 404);
        }

        // Verifikasi: hash_token cocok ATAU model_id cocok (QR lama sebelum register)
        $matchedBy = $device->hash_token === $token ? 'hash_token' : ($device->model_id === $token ? 'model_id' : null);
        $match = $matchedBy !== null;

        return response()->json([
            'valid' => $match,
            'token_input' => $token,
            'matched_by' => $matchedBy,
            'message' => $match ? '✓ Token cocok! Perangkat terverifikasi ('.($matchedBy === 'hash_token' ? 'JWT hash' : 'Model ID').').' : 'Token tidak sesuai dengan data perangkat.',
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
