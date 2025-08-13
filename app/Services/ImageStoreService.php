<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageStoreService implements Service
{
    public function execute($data): array
    {
        if (! $image = Storage::drive('public')->put('images', $data)) {
            throw new \Exception("Failed to upload image");
        }

        $image = Image::create([
            'name' => $data->getClientOriginalName(),
            'url' => Storage::url($image),
        ]);

        if (! $image) {
            Storage::disk('public')->delete(Storage::url($image));
            throw new \Exception("Failed to create image registry");
        }

        return $image->toArray();
    }
}
