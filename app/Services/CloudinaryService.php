<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public function __construct(private Cloudinary $cloudinary) {}

    /**
     * Upload a file to Cloudinary.
     *
     * Returns the secure URL and a composite public_id in the format
     * "resource_type:public_id" so the resource type is preserved for deletion.
     *
     * @return array{url: string, public_id: string}
     */
    public function upload(UploadedFile $file, string $folder = 'rukuni'): array
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'        => $folder,
            'resource_type' => 'auto',
        ]);

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['resource_type'] . ':' . $result['public_id'],
        ];
    }

    /**
     * Delete a file from Cloudinary.
     *
     * Expects $publicId in the composite format "resource_type:public_id".
     */
    public function delete(string $publicId): void
    {
        [$resourceType, $id] = explode(':', $publicId, 2);

        $this->cloudinary->uploadApi()->destroy($id, ['resource_type' => $resourceType]);
    }
}
