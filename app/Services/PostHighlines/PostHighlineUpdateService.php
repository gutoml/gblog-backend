<?php

namespace App\Services\PostHighlines;

use App\Models\PostHighline;
use App\Services\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostHighlineUpdateService extends PostHighlineService implements Service
{
    public function execute(array $data): array
    {
        PostHighline::truncate();
        $result = DB::transaction(function() use ($data): Collection {
            return parent::handle($data['posts']);
        });

        return $result->toArray();
    }
}
