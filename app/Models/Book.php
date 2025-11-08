<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * Represents a book entity within the system.
 *
 * Each book record stores its basic metadata (ISBN, title, author, etc.)
 * and is associated with a genre and multiple physical copies.
 */
class Book extends BaseModel
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
    protected $table = 'books';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'isbn',
        'title',
        'author',
        'genre_id',
        'publisher',
    ];

    /**
     * Converts the binary UUID from the database to a string UUID.
     *
     * @param mixed $value binary UUID value
     *
     * @return null|string string UUID or null if not set
     */
    public function getGenreIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Defines the relationship between the book and its genre.
     *
     * @return BelongsTo the genre that this book belongs to
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Defines the relationship between the book and its copies.
     *
     * Each book can have multiple copies available for loans.
     *
     * @return HasMany the collection of copies associated with this book
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }
}
