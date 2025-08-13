<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
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

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_relationships', 'post_id', 'related_post_id')
            ->withTimestamps();
    }

    public function relatedFromPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_relationships', 'related_post_id', 'post_id')
            ->withTimestamps();
    }

    public function allRelatedPosts(): Collection
    {
        return $this->relatedPosts->merge($this->relatedFromPosts);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }

    public function recordView(): Model
    {
        $this->views()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return $this;
    }

    public function postHighlines(): HasMany
    {
        return $this->hasMany(PostHighline::class);
    }
}
