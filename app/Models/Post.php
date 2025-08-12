<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'slug',
    ];

    /**
     * Summary of user
     * @return BelongsTo<User, Post>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Summary of category
     * @return BelongsTo<Category, Post>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_relationships', 'post_id', 'related_post_id')
            ->withTimestamps();
    }

    public function relatedFromPosts()
    {
        return $this->belongsToMany(Post::class, 'post_relationships', 'related_post_id', 'post_id')
            ->withTimestamps();
    }

    // Método para todos os posts relacionados (bidirecional)
    public function allRelatedPosts()
    {
        return $this->relatedPosts->merge($this->relatedFromPosts);
    }
}
