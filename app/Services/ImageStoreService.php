<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageStoreService implements Service
{
    public function execute($data): mixed
    {
        if (! $image = Storage::drive('public')->put('images', $data)) {
            throw new \Exception("Failed to upload image");
        }

        return [
            'name' => $data->getClientOriginalName(),
            'url' => Storage::url($image),
        ];
    }
}
