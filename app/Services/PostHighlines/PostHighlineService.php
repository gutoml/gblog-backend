<?php

namespace App\Services\PostHighlines;

use App\Models\PostHighline;
use Illuminate\Support\Collection;

class PostHighlineService
{
    protected function handle(array $data): Collection
    {
        $postHighlinesCollection = new Collection();

        foreach ($data as $postHighline) {
            $postHighlinesCollection->push(PostHighline::create($postHighline));
        }

        return $postHighlinesCollection;
    }
}
