<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category' => [
                'name' => $this->category['name'],
                'slug' => $this->category['slug'],
            ],
            'image' => [
                'name' => $this->images[0]['name'],
                'url' => $this->images[0]['url'],
            ],
            'title' => $this->title,
            'content' => $this->content,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
