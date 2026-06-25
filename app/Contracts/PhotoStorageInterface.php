<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface PhotoStorageInterface
{
    public function save(UploadedFile $file, int|string $entityId): string;

    public function delete(string $path): void;
}
