<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends BaseModel
{
    protected $table = 'vw_school_classes';
    public $timestamps = false;

    protected $guarded = [];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'vw_student_school_class',
            'school_class_id',
            'student_id',
        );
    }
}
