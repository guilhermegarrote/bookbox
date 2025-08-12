<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Book extends BaseModel
{
    protected $table = 'books';
    public $timestamps = false;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'genre_id',
        'publisher',
    ];

    public function getGenreIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }
}
