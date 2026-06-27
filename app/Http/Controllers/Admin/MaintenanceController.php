<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceCheck;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Maintenance', [
            'recentChecks' => fn () => DeviceCheck::with('phoneList:id,brand_id,model_id,model_name')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->latest()
                ->take(200)
                ->get()
                ->unique('phone_list_id')
                ->values(),
        ]);
    }

    public function getDeviceByQr(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model_id' => 'string|required',
        ]);

        $device = PhoneList::where('model_id', $validated['model_id'])->first();

        if (! $device) {
            return response()->json(['errors' => ['model_id' => 'Perangkat tidak ditemukan!']], 404);
        }

        return response()->json($device);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|string|exists:phone_lists,model_id',
            'keterangan' => 'nullable|string|max:2000',
            'imei_ok' => 'boolean',
            'mac_ok' => 'boolean',
            'foto' => 'nullable|array',
            'foto.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $phoneList = PhoneList::where('model_id', $validated['id'])->firstOrFail();

        // Get admin user from session
        $adminUser = session('admin_user');

        // Handle foto uploads
        $fotoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store("device/{$phoneList->id}/check", 'public');
                $fotoPaths[] = $path;
            }
        }

        $check = DeviceCheck::create([
            'phone_list_id' => $phoneList->id,
            'checked_by_name' => $adminUser['name'] ?? 'Unknown',
            'checked_by_username' => $adminUser['username'] ?? 'unknown',
            'imei_ok' => $validated['imei_ok'] ?? false,
            'mac_ok' => $validated['mac_ok'] ?? false,
            'keterangan' => $validated['keterangan'] ?? null,
            'foto' => ! empty($fotoPaths) ? $fotoPaths : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengecekan berhasil dicatat.',
            'check' => $check->load('phoneList'),
        ]);
    }
}
