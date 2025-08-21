<?php

namespace App\Models\View;

use App\Models\Copy;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Book extends BaseModel
{
    protected $table = 'vw_books';
    public $timestamps = false;

    protected $guarded = [];

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
