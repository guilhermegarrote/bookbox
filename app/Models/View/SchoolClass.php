<?php

declare(strict_types=1);

namespace App\Models\View;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/*Represents a view model for school classes.
 * This model maps to the database view `vw_school_classes`, containing aggregated
 * information about each class and its relationship with students.
 */
class SchoolClass extends BaseModel
{
    /** @var bool Indicates if the model should be timestamped. */
    public $timestamps = false;

    /** @var string The database table (view) associated with the model. */
    protected $table = 'vw_school_classes';

    /** @var array<int, string> The attributes that aren’t mass assignable. */
    protected $guarded = [];

    /**
     * Defines the many-to-many relationship between classes and students.
     * Uses the view `vw_student_school_class` as the pivot table.
     */
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
