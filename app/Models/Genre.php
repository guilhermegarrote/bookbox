<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Represents a book genre in the system.
 *
 * Each genre can have multiple books associated with it.
 */
class Genre extends BaseModel
{
    /**
     * Indicates that the model does not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table used by this model.
     *
     * @var string
     */
    protected $table = 'genres';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color_hex',
    ];

    /**
     * Defines the relationship between a genre and its books.
     *
     * @return HasMany the collection of books associated with this genre
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function copies(): HasManyThrough
    {
        return $this->hasManyThrough(
            Copy::class,
            Book::class
        );
    }
}
