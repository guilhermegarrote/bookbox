<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Copy extends BaseModel
{
    protected $table = 'vw_copies';
    public $timestamps = false;

    protected $guarded = [];

    public function getBookIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

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

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
