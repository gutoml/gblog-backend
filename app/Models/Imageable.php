<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imageable extends Model
{
    protected $table = 'imageables';

    protected $fillable = [
        'image_id',
        'imageable_id',
        'imageable_type',
    ];

    /**
     * Relacionamento com a imagem
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Relacionamento polimórfico
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
