<?php

namespace App\Services\Posts;

use App\Models\Post;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class PostStoreService implements Service
{
    public function execute(array $data): array
    {
        $response = DB::transaction(function() use ($data) {
            $post = Post::create($data);
            $post->images()->attach($data['image_id']);
            $post->relatedPosts()->attach($data['related_posts']);
            $post->load(['user', 'category', 'images', 'relatedPosts']);

            return $post->toArray();
        });

        return $response;
    }
}
