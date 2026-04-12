<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    /**
     * Upload a file and return the secure URL and public_id.
     */
    public function upload(UploadedFile $file, string $folder = 'rukuni'): array
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'auto', // handles images, videos, raw files
        ]);

        return [
            'url'       => $result->getSecurePath(),
            'public_id' => $result->getPublicId(),
        ];
    }

    /**
     * Delete a file by its public_id.
     */
    public function delete(string $publicId): void
    {
        Cloudinary::destroy($publicId);
    }
}