<?php

declare(strict_types=1);

namespace App\Models\View;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a view model for students.
 * This model maps to the database view `vw_students`, which stores
 * decrypted and formatted student data for display and processing.
 */
class Student extends BaseModel
{
    /** @var bool Indicates if the model should be timestamped. */
    public $timestamps = false;

    /** @var string The database table (view) associated with the model. */
    protected $table = 'vw_students';

    /** @var array<int, string> The attributes that aren’t mass assignable. */
    protected $guarded = [];

    /** @var array<int, string> Attributes that should be hidden from serialization. */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    /**
     * Decrypts and returns the student's CPF.
     *
     * @param null|string $value Encrypted CPF value
     *
     * @return null|string Decrypted CPF or null if unavailable
     */
    public function getCpfAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Decrypts and returns the student's email.
     *
     * @param null|string $value Encrypted email value
     *
     * @return null|string Decrypted email or null if unavailable
     */
    public function getEmailAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Decrypts and formats the student's phone number.
     *
     * Example output: (12) 34567-8901
     *
     * @param null|string $value Encrypted phone value
     *
     * @return null|string Decrypted and formatted phone number, or null if unavailable
     */
    public function getPhoneAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $decrypted = Utils::decrypt($value);

        $numbersOnly = preg_replace('/\D/', '', $decrypted);

        if (\strlen($numbersOnly) === 11) {
            return \sprintf(
                '(%s) %s-%s',
                substr($numbersOnly, 0, 2),
                substr($numbersOnly, 2, 5),
                substr($numbersOnly, 7),
            );
        }

        return $decrypted;
    }

    /**
     * Defines the many-to-many relationship between students and school classes.
     * Uses the view `vw_student_school_class` as the pivot table.
     */
    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'vw_student_school_class',
            'student_id',
            'school_class_id',
        );
    }
}
