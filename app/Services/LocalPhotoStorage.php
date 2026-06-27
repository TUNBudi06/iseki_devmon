<?php

namespace App\Services;

use App\Contracts\PhotoStorageInterface;
use Illuminate\Http\UploadedFile;

class LocalPhotoStorage implements PhotoStorageInterface
{
    public function save(UploadedFile $file, int|string $entityId, string $subfolder = ''): string
    {
        $basePath = 'storage/device/'.$entityId;
        if ($subfolder !== '') {
            $basePath .= '/'.$subfolder;
        }
        $directory = public_path($basePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $hash = md5_file($file->getRealPath());
        $ext = $file->getClientOriginalExtension();
        $filename = $hash.'.'.$ext;
        $fullPath = $directory.'/'.$filename;

        if (file_exists($fullPath)) {
            return $basePath.'/'.$filename;
        }

        $file->move($directory, $filename);

        return $basePath.'/'.$filename;
    }

    public function delete(string $path): void
    {
        $full = public_path($path);
        if (file_exists($full)) {
            unlink($full);
        }
    }
}
