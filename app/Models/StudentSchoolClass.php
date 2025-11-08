<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * Represents the pivot table linking students and school classes.
 * Provides relationships to the Student and SchoolClass models.
 */
class StudentSchoolClass extends BaseModel
{
    /**
     * Indicates if the model should not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_school_class';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'school_class_id',
    ];

    /**
     * Get the decrypted and formatted student ID.
     *
     * @param null|string $value Binary UUID
     *
     * @return null|string String UUID
     */
    public function getStudentIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Get the decrypted and formatted school class ID.
     *
     * @param null|string $value Binary UUID
     *
     * @return null|string String UUID
     */
    public function getSchoolClassIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Relationship: Belongs to a student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship: Belongs to a school class.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
