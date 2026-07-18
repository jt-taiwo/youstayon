<?php

declare(strict_types=1);

namespace App\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FileStorageService
{
    public function storeAvatar(UploadedFile $file): string
    {
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs(
            'avatars/users',
            $filename,
            'public',
        );
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}