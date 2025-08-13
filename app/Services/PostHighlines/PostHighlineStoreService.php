<?php

namespace App\Services\PostHighlines;

use App\Services\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostHighlineStoreService extends PostHighlineService implements Service
{
    public function execute(array $data): array
    {
        $result = DB::transaction(function() use ($data): Collection {
            return parent::handle($data['posts']);
        });

        return $result->toArray();
    }
}
