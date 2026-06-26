<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\PhotoStorageInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneStoreRequest;
use App\Http\Requests\PhoneUpdateRequest;
use App\Models\Brand;
use App\Models\Departemen;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListDeviceController extends Controller
{
    public function __construct(
        private readonly PhotoStorageInterface $photoStorage,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/ListDevice', [
            'brands' => fn () => Brand::orderBy('created_at', 'desc')->get(),
            'phoneLists' => fn () => PhoneList::with('brand')
                ->orderByRaw('deleted_at IS NOT NULL')
                ->orderBy('created_at', 'desc')
                ->withTrashed()
                ->take(300)
                ->get(),
            'departemenOptions' => fn () => Departemen::orderBy('id')->get(),
        ]);
    }

    // ─── Brand CRUD ────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:255', 'unique:brands,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Brand::create($validated);

        return response()->json(['message' => 'Brand berhasil ditambahkan']);
    }

    public function updateBrand(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Brand::where('id', $id)->update(['name' => $validated['name']]);

        return response()->json(['message' => 'Brand berhasil diperbarui']);
    }

    public function destroyBrand(string $id): JsonResponse
    {
        Brand::where('id', $id)->delete();

        return response()->json(['message' => 'Brand berhasil dihapus']);
    }

    // ─── Phone List CRUD ───────────────────────────────────────

    public function storePhone(PhoneStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = collect($validated)->except(['list_photos', 'imei', 'mac_address'])->toArray();
        $data['registered'] = false;
        $data['approved'] = true;
        $data['hash_token'] = null;

        if (! empty($validated['imei'])) {
            $data['imei'] = $validated['imei'];
        }
        if (! empty($validated['mac_address'])) {
            $data['mac_address'] = $validated['mac_address'];
        }

        // Create phone first to get the ID
        $phone = PhoneList::create($data);

        // Store photos
        if ($request->hasFile('list_photos')) {
            $paths = [];
            foreach ($request->file('list_photos') as $i => $photo) {
                $url = $this->photoStorage->save($photo, $phone->id);
                $paths[] = $url;
            }
            $data['list_photos'] = $paths;
            // First photo becomes the thumbnail
            $data['thumbnail'] = $paths[0];
        }

        $phone->update($data);

        return to_route('admin.listDevice')->with('success', 'Phone berhasil ditambahkan');
    }

    public function updatePhone(PhoneUpdateRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();

        $phone = PhoneList::findOrFail($id);

        $data = collect($validated)->except(['list_photos', 'imei', 'mac_address'])->toArray();

        // Handle IMEI/MAC — update if provided, set null if empty
        if ($request->has('imei')) {
            $data['imei'] = $validated['imei'] ?: null;
        }
        if ($request->has('mac_address')) {
            $data['mac_address'] = $validated['mac_address'] ?: null;
        }

        // Handle thumbnail selection (URL string from existing photos)
        if ($request->filled('thumbnail') && ! $request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->input('thumbnail');
        } elseif (! $request->has('thumbnail') || ! $request->filled('thumbnail')) {
            // If thumbnail is not sent or empty, keep the existing DB value
            unset($data['thumbnail']);
        }

        // Append new photos — first uploaded photo becomes thumbnail
        // (frontend reorders so the picked thumbnail is first)
        if ($request->hasFile('list_photos')) {
            $existing = (array) ($phone->list_photos ?? []);
            foreach ($request->file('list_photos') as $i => $photo) {
                $url = $this->photoStorage->save($photo, $phone->id);
                if (! in_array($url, $existing, true)) {
                    $existing[] = $url;
                }
                if ($i === 0) {
                    $data['thumbnail'] = $url;
                }
            }
            $data['list_photos'] = $existing;
        }

        $phone->update($data);

        return to_route('admin.listDevice')->with('success', 'Phone berhasil diperbarui');
    }

    /**
     * Delete a single photo from the phone's gallery.
     * If the deleted photo was the thumbnail, promote the next photo.
     */
    public function destroyPhonePhoto(Request $request, string $id): JsonResponse
    {
        $request->validate(['photo_url' => ['required', 'string']]);

        $phone = PhoneList::findOrFail($id);
        $photoUrl = $request->input('photo_url');

        $photos = (array) ($phone->list_photos ?? []);
        $photos = array_values(array_filter($photos, fn ($p) => $p !== $photoUrl));

        $this->photoStorage->delete($photoUrl);

        $update = ['list_photos' => $photos];

        // If deleted photo was the thumbnail, promote first remaining photo
        if ($phone->thumbnail === $photoUrl) {
            $update['thumbnail'] = $photos[0] ?? null;
        }

        $phone->update($update);

        return response()->json(['message' => 'Foto berhasil dihapus']);
    }

    public function destroyPhone(string $id): JsonResponse
    {
        $phone = PhoneList::withTrashed()->findOrFail($id);

        if ($phone->trashed()) {
            // Already soft deleted - force delete (cleanup)
            $phone->forceDelete();
        } else {
            // Soft delete (keep files and data for logs)
            $phone->delete();
        }

        return response()->json(['message' => 'Phone berhasil dihapus']);
    }

    public function approvePhone(PhoneList $phone): RedirectResponse
    {
        $phone->update(['approved' => true]);

        return redirect()->back()->with('success', 'Perangkat berhasil disetujui');
    }

    // ─── Departemen CRUD ────────────────────────────────────────

    public function storeDepartemen(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:50', 'unique:departemens,id'],
            'name' => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'max:20'],
        ]);

        Departemen::create($validated);

        return response()->json(['message' => 'Departemen berhasil ditambahkan']);
    }

    public function updateDepartemen(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['required', 'string', 'max:20'],
        ]);

        Departemen::where('id', $id)->update([
            'name' => $validated['name'],
            'color' => $validated['color'],
        ]);

        return response()->json(['message' => 'Departemen berhasil diperbarui']);
    }

    public function destroyDepartemen(string $id): JsonResponse
    {
        // Check if any phone lists reference this departemen
        $inUse = PhoneList::where('departemen', $id)->exists();

        if ($inUse) {
            return response()->json([
                'message' => 'Tidak dapat menghapus departemen yang masih digunakan oleh perangkat. Ubah departemen perangkat terlebih dahulu.',
            ], 422);
        }

        Departemen::where('id', $id)->delete();

        return response()->json(['message' => 'Departemen berhasil dihapus']);
    }
}
