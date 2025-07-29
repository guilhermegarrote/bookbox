<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends BaseModel
{
    protected $table = 'school_classes';
    public $timestamps = false;

    protected $fillable = [
        'course',
        'term',
        'start_date',
        'end_date',
    ];

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
