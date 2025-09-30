<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class StudentSchoolClass extends BaseModel
{
    protected $table = 'student_school_class';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'school_class_id',
    ];

    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getSchoolClassIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
