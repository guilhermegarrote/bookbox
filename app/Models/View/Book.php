<?php

declare(strict_types=1);

namespace App\Models\View;

use App\Models\Copy;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * View model representing books.
 */
class Book extends BaseModel
{
    /** @var bool */
    public $timestamps = false;

    /** @var string */
    protected $table = 'vw_books';

    /** @var array<int, string> */
    protected $guarded = [];

    /**
     * Converts binary UUID (BLOB) to string for genre_id.
     *
     * @param null|string $value
     */
    public function getGenreIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Formats ISBN as a readable 13-digit string.
     *
     * @param null|string $value
     */
    public function getIsbnAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $numbersOnly = preg_replace('/\D/', '', $value);

        if (\strlen($numbersOnly) === 13) {
            return \sprintf(
                '%s-%s-%s-%s-%s',
                substr($numbersOnly, 0, 3),
                substr($numbersOnly, 3, 1),
                substr($numbersOnly, 4, 4),
                substr($numbersOnly, 8, 4),
                substr($numbersOnly, 12, 1),
            );
        }

        return $numbersOnly;
    }

    /**
     * Book belongs to a Genre.
     *
     * @return BelongsTo<Genre, Book>
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Book has many Copies.
     *
     * @return HasMany<Copy>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    /**
     * Returns filterable book data (genre_name, publisher).
     *
     * @param null|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFilterData($query = null)
    {
        $query = $query ?? static::query();

        $query->getQuery()->orders = null;

        return $query
            ->select('genre_name', 'publisher')
            ->orderBy('genre_name')
            ->orderBy('publisher')
            ->get();
    }
}
