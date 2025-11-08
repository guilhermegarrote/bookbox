<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Represents a loan (borrowed copy) in the system.
 *
 * Each loan is associated with a student and a specific copy of a book.
 * Automatically generates a unique barcode code and sets the start date on creation.
 */
class Loan extends BaseModel
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
    protected $table = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'barcode_code',
        'copy_id',
        'start_date',
        'due_date',
        'returned_date',
    ];

    /**
     * Converts the stored binary student ID to UUID string.
     *
     * @param mixed $value
     */
    public function getStudentIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts the stored binary copy ID to UUID string.
     *
     * @param mixed $value
     */
    public function getCopyIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Generates a unique barcode code for the loan.
     *
     * @param string $prefix prefix to prepend to the code
     * @param int $length length of the random part of the code
     */
    public static function generateBarCode(string $prefix = 'LN', int $length = 8): string
    {
        do {
            $code = $prefix . strtoupper(Str::random($length));
        } while (self::where('barcode_code', $code)->exists());

        return $code;
    }

    /**
     * Defines the relationship to the student who borrowed the copy.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Defines the relationship to the borrowed copy.
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Boot method to handle model events.
     *
     * Automatically sets the barcode and start date when creating a new loan.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($loan) {
            if (empty($loan->barcode_code)) {
                $loan->barcode_code = self::generateBarCode();
            }

            if (empty($loan->start_date)) {
                $loan->start_date = now()->toDateString();
            }
        });
    }
}
