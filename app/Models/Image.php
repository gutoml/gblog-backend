<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
    ];

    /**
     * Relacionamento polimórfico com outras entidades
     */
    public function imageables(): HasMany
    {
        return $this->hasMany(Imageable::class);
    }

    /**
     * Obtém todos os modelos relacionados
     */
    public function models(): MorphToMany
    {
        return $this->morphedByMany(Imageable::class, 'imageable');
    }
}
