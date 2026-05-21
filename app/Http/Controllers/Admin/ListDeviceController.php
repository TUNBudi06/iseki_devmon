<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ListDeviceController extends Controller
{
    public function index(): Response
    {
        $devices = DB::table('brands')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/ListDevice', [
            'devices' => $devices,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:255', 'unique:brands,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::table('brands')->insert([
            'id' => $validated['id'],
            'name' => $validated['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.listDevice')->with('success', 'Perangkat berhasil ditambahkan');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::table('brands')->where('id', $id)->update([
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.listDevice')->with('success', 'Perangkat berhasil diperbarui');
    }

    public function destroy(string $id): RedirectResponse
    {
        DB::table('brands')->where('id', $id)->delete();

        return redirect()->route('admin.listDevice')->with('success', 'Perangkat berhasil dihapus');
    }
}
