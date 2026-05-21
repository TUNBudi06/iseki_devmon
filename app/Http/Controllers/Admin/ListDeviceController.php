<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListDeviceController extends Controller
{
    public function index(): Response
    {
        $devices = Brand::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/ListDevice', [
            'devices' => $devices,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:255', 'unique:brands,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Brand::insert([
            'id' => $validated['id'],
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Perangkat berhasil ditambahkan',
            'device' => [
                'id' => $validated['id'],
                'name' => $validated['name'],
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Brand::where('id', $id)->update([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Perangkat berhasil diperbarui',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        Brand::where('id', $id)->delete();

        return response()->json([
            'message' => 'Perangkat berhasil dihapus',
        ]);
    }
}
