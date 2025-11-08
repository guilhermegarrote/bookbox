<?php

declare(strict_types=1);

namespace App\Models\View;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * View model representing book copies.
 */
class Copy extends BaseModel
{
    public $timestamps = false;
    protected $table = 'vw_copies';
    protected $guarded = [];

    public function getBookIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getGenreIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

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

    /** @return BelongsTo<Book, Copy> */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /** @return HasMany<Loan> */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
