<?php

namespace App\Services\Posts;

use App\Models\Post;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class PostUpdateService implements Service
{
    public function __construct(
        private Post $post
    ) {}

    public function execute(array $data): array
    {
        $response = DB::transaction(function() use ($data) {
            $this->post->update($data);
            $this->post->images()->sync($data['image_id']);
            $this->post->relatedPosts()->sync($data['related_posts']);
            $this->post->load(['user', 'category', 'images', 'relatedPosts']);

            return $this->post->toArray();
        });

        return $response;
    }
}
