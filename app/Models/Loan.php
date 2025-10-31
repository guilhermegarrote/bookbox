<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Loan extends BaseModel
{
    protected $table = 'loans';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'barcode_code',
        'copy_id',
        'start_date',
        'due_date',
        'returned_date'
    ];

    protected static function boot()
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

    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getCopyIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public static function generateBarCode(string $prefix = 'LN', int $length = 8): string
    {
        do {
            $code = $prefix . strtoupper(Str::random($length));
        } while (self::where('barcode_code', $code)->exists());

        return $code;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }
}
