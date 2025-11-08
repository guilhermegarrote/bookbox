<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a school class/course in the system.
 *
 * A school class can have many students enrolled.
 */
class SchoolClass extends BaseModel
{
    /**
     * Indicates if the model should not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table used by this model.
     *
     * @var string
     */
    protected $table = 'school_classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course',
        'term',
        'start_date',
        'end_date',
    ];

    /**
     * Defines the many-to-many relationship with students.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'student_school_class',
            'school_class_id',
            'student_id',
        );
    }
}
