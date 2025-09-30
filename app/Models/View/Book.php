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

    public function getIsbnAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $numbersOnly = preg_replace('/\D/', '', $value);

        if (strlen($numbersOnly) === 13) {
            return sprintf(
                '%s-%s-%s-%s-%s',
                substr($numbersOnly, 0, 3),
                substr($numbersOnly, 3, 1),
                substr($numbersOnly, 4, 4),
                substr($numbersOnly, 8, 4),
                substr($numbersOnly, 12, 1)
            );
        }

        return $numbersOnly;
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    /**
     * Returns data for filters (genre_name, publisher)
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @return \Illuminate\Support\Collection
     */
    public static function getFilterData($query = null)
    {
        $query = $query ?? static::query();

        return $query->select('genre_name', 'publisher')
            ->groupBy('genre_name', 'publisher')
            ->orderBy('genre_name')
            ->orderBy('publisher')
            ->get();
    }
}
