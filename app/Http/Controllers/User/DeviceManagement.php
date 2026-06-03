<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helper\JwtManager;
use App\Models\Brand;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceManagement extends Controller
{
    public function index()
    {
        return Inertia::render('Member/PhoneNotRegister');
    }

    public function checkDeviceToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string'],
        ]);

        $device = PhoneList::where('model_id', $validated['device_id'])->first();

        if (! $device || ! $device->approved || ! $device->registered) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json(['valid' => true]);
    }

    public function registerViaQR()
    {
        return Inertia::render('Member/PhoneRegisterViaQR');
    }

    /**
     * Verify a device by model_id or hash_token.
     * Returns device info if found and not yet registered.
     */
    public function verifyDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $validated['code'];

        // Look up by model_id first, then hash_token
        $device = PhoneList::where('model_id', $code)
            ->orWhere('hash_token', $code)
            ->first();

        if (! $device) {
            return response()->json([
                'found' => false,
                'message' => 'Perangkat tidak ditemukan di database.',
            ], 404);
        }

        if ($device->registered) {
            return response()->json([
                'found' => true,
                'registered' => true,
                'message' => 'Perangkat sudah terdaftar sebelumnya.',
            ], 422);
        }

        return response()->json([
            'found' => true,
            'registered' => false,
            'device' => [
                'id' => $device->id,
                'model_id' => $device->model_id,
                'model_name' => $device->model_name,
                'model_type' => $device->model_type,
                'brand_name' => $device->brand?->name,
                'thumbnail' => $device->thumbnail,
            ],
        ]);
    }

    /**
     * Register (activate) a device after QR verification.
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model_id' => ['required', 'string', 'exists:phone_lists,model_id'],
            'imei' => ['nullable', 'string', 'max:17', 'unique:phone_lists,imei'],
            'mac_address' => ['nullable', 'string', 'max:17', 'unique:phone_lists,mac_address'],
        ]);

        // Minimal salah satu wajib diisi
        if (empty($validated['imei']) && empty($validated['mac_address'])) {
            return response()->json([
                'success' => false,
                'message' => 'Harap isi IMEI atau MAC Address perangkat (minimal salah satu).',
            ], 422);
        }

        $device = PhoneList::where('model_id', $validated['model_id'])
            ->where('registered', false)
            ->first();

        if (! $device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan atau sudah terdaftar.',
            ], 422);
        }

        // Generate JWT and hash
        $jwtManager = new JwtManager($device->model_name, $device->model_id);
        $jwt = $jwtManager->encode();
        $hash = $jwtManager->hashJwt();

        $device->update([
            'registered' => true,
            'approved' => true,
            'hash_token' => $hash,
            'imei' => $validated['imei'] ?? null,
            'mac_address' => $validated['mac_address'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan!',
            'jwt' => $jwt,
            'device_id' => $device->model_id,
        ]);
    }

    /**
     * Show the manual register form.
     */
    public function registerManual()
    {
        $brands = Brand::orderBy('name')->get();

        $last = PhoneList::where('model_id', 'like', 'dev-%')
            ->orderByRaw('CAST(SUBSTRING(model_id, -3) AS UNSIGNED) DESC')
            ->first();
        $lastIncrement = $last ? (int) substr($last->model_id, -3) : 0;
        $nextIncrement = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);

        return Inertia::render('Member/PhoneRegisterManual', [
            'brands' => $brands,
            'nextIncrement' => $nextIncrement,
        ]);
    }

    /**
     * Store a device from manual member registration.
     * Generates JWT and sets registered=true (linked to device).
     * Requires admin approval before login.
     */
    public function storeManualDevice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brand_id' => ['required', 'string', 'exists:brands,id'],
            'model_id' => ['required', 'string', 'max:255', 'unique:phone_lists,model_id'],
            'model_name' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255'],
            'buy_date' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string', 'max:255'],
            'storage' => ['required', 'string', 'max:255'],
            'imei' => ['nullable', 'string', 'max:17', 'unique:phone_lists,imei'],
            'mac_address' => ['nullable', 'string', 'max:17', 'unique:phone_lists,mac_address'],
        ]);

        $data = collect($validated)->except(['imei', 'mac_address'])->toArray();
        $data['registered'] = true;
        $data['approved'] = false;
        $data['hash_token'] = null;

        // Simpan IMEI/MAC hanya jika diisi
        if (! empty($validated['imei'])) {
            $data['imei'] = $validated['imei'];
        }
        if (! empty($validated['mac_address'])) {
            $data['mac_address'] = $validated['mac_address'];
        }

        $phone = PhoneList::create($data);

        // Generate JWT
        $jwtManager = new JwtManager($phone->model_name, $phone->model_id);
        $jwt = $jwtManager->encode();
        $hash = $jwtManager->hashJwt();

        $phone->update(['hash_token' => $hash]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil didaftarkan! Menunggu persetujuan admin.',
            'jwt' => $jwt,
            'device_id' => $phone->model_id,
        ]);
    }
}
