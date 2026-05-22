<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\PhoneList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;

class ListDeviceController extends Controller
{
    public function index(): Response
    {
        $brands = Brand::orderBy('created_at', 'desc')->get();
        $phoneLists = PhoneList::with('brand')
            ->orderByRaw('deleted_at IS NOT NULL')
            ->orderBy('created_at', 'desc')
            ->withTrashed()
            ->get();

        return Inertia::render('Admin/ListDevice', [
            'brands' => $brands,
            'phoneLists' => $phoneLists,
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

        Brand::where('id', $id)->update([
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Brand berhasil diperbarui']);
    }

    public function destroyBrand(string $id): JsonResponse
    {
        Brand::where('id', $id)->delete();

        return response()->json(['message' => 'Brand berhasil dihapus']);
    }

    // ─── Photo Helpers ─────────────────────────────────────────

    /**
     * Save an uploaded photo under public/storage/device/{phoneId}/
     * Uses CRC32 of file content for the filename to avoid duplicates.
     * Returns the public URL path like "storage/device/{id}/{hash}.{ext}"
     */
    private function savePhoto(UploadedFile $file, int $phoneId): string
    {
        $basePath = 'storage/device/'.$phoneId;
        $directory = public_path($basePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // CRC32 hash of file content for dedup
        $hash = dechex(crc32(file_get_contents($file->getRealPath())));
        $ext = $file->getClientOriginalExtension();
        $filename = $hash.'.'.$ext;
        $fullPath = $directory.'/'.$filename;

        // If file with same hash already exists, return existing URL
        if (file_exists($fullPath)) {
            return $basePath.'/'.$filename;
        }

        $file->move($directory, $filename);

        return $basePath.'/'.$filename;
    }

    /**
     * Delete a photo file given its public path (e.g. "storage/device/1/abc123.jpg")
     */
    private function deletePhotoByPath(string $path): void
    {
        $full = public_path($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }

    // ─── Phone List CRUD ───────────────────────────────────────

    public function storePhone(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'string', 'max:255', 'unique:phone_lists,model_id'],
            'model_name' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255'],
            'buy_date' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string', 'max:255'],
            'storage' => ['required', 'string', 'max:255'],
            'list_photos' => ['nullable', 'array'],
            'list_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $data = collect($validated)->except(['list_photos'])->toArray();
        $data['registered'] = false;
        $data['hash_token'] = null;

        // Create phone first to get the ID
        $phone = PhoneList::create($data);

        // Store photos
        if ($request->hasFile('list_photos')) {
            $paths = [];
            foreach ($request->file('list_photos') as $i => $photo) {
                $url = $this->savePhoto($photo, $phone->id);
                $paths[] = $url;
            }
            $data['list_photos'] = $paths;
            // First photo becomes the thumbnail
            $data['thumbnail'] = $paths[0];
        }

        $phone->update($data);

        return to_route('admin.listDevice')->with('success', 'Phone berhasil ditambahkan');
    }

    public function updatePhone(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'string', 'max:255', 'unique:phone_lists,model_id,'.$id],
            'model_name' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255'],
            'buy_date' => ['required', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'ram' => ['required', 'string', 'max:255'],
            'storage' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string'],
            'list_photos' => ['nullable', 'array'],
            'list_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $phone = PhoneList::findOrFail($id);

        $data = collect($validated)->except(['list_photos'])->toArray();

        // Handle thumbnail selection (URL string from existing photos)
        if ($request->filled('thumbnail') && ! $request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->input('thumbnail');
        } elseif (! $request->has('thumbnail') || ! $request->filled('thumbnail')) {
            // If thumbnail is not sent or empty, keep the existing DB value
            unset($data['thumbnail']);
        }

        // Append new photos
        if ($request->hasFile('list_photos')) {
            $existing = (array) ($phone->list_photos ?? []);
            foreach ($request->file('list_photos') as $photo) {
                $url = $this->savePhoto($photo, $phone->id);
                if (! in_array($url, $existing, true)) {
                    $existing[] = $url;
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

        $this->deletePhotoByPath($photoUrl);

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
}
