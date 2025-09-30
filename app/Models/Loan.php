<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Loan extends BaseModel
{
    protected $table = 'loans';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'copy_id',
        'start_date',
        'due_date',
        'returned_date',
    ];

    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getCopyIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
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
