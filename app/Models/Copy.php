<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * Represents a physical copy of a book in the system.
 *
 * Each copy is linked to a specific book and can have multiple loans associated with it.
 */
class Copy extends BaseModel
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
    protected $table = 'copies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'number',
    ];

    /**
     * Converts the binary UUID from the database to a string UUID.
     *
     * @param mixed $value binary UUID value
     *
     * @return null|string string UUID or null if not set
     */
    public function getBookIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Defines the relationship between a copy and its parent book.
     *
     * @return BelongsTo the book that this copy belongs to
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Defines the relationship between a copy and its loans.
     *
     * A copy can be loaned multiple times, each recorded as a Loan entity.
     *
     * @return HasMany the collection of loans associated with this copy
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
