<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends BaseModel
{
    protected $table = 'genres';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'color_hex',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
