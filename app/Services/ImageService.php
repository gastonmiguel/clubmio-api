<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    public function saveImage($imageData, $directory = 'partners')
    {
        $imageData = base64_decode($imageData);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
            return false;
        }

        $image = Image::make($imageData);

        $imageName = uniqid() . '.webp';
        $imagePath = $directory . '/' . $imageName;

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        try {
            $image->encode('webp', 100);
            Storage::disk('public')->put($imagePath, $image->__toString());
            return $imagePath;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function deleteImage($imagePath)
    {
        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->delete($imagePath);
        }

        return false;
    }
}
