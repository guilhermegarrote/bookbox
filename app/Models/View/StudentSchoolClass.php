<?php

declare(strict_types=1);

namespace App\Models\View;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * Represents the view `vw_student_school_class`, which links students and school classes,
 * including decrypted and formatted personal data (CPF, email, phone) for display and filtering.
 */
class StudentSchoolClass extends BaseModel
{
    /** @var bool Indicates if the model should be timestamped. */
    public $timestamps = false;

    /** @var string The database view associated with the model. */
    protected $table = 'vw_student_school_class';

    /** @var array<int, string> The attributes that aren’t mass assignable. */
    protected $guarded = [];

    /** @var array<int, string> Attributes that should be hidden from serialization. */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    /**
     * Converts the binary UUID of the student to a string.
     *
     * @param null|string $value Binary UUID value
     *
     * @return null|string UUID as string or null
     */
    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts the binary UUID of the school class to a string.
     *
     * @param null|string $value Binary UUID value
     *
     * @return null|string UUID as string or null
     */
    public function getSchoolClassIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Decrypts and formats the student's CPF.
     *
     * Example output: 123.456.789-00
     *
     * @param null|string $value Encrypted CPF
     *
     * @return null|string Decrypted and formatted CPF, or null if unavailable
     */
    public function getCpfAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $decrypted = Utils::decrypt($value);
        $numbersOnly = preg_replace('/\D/', '', $decrypted);

        if (preg_match('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', $numbersOnly, $matches)) {
            return "{$matches[1]}.{$matches[2]}.{$matches[3]}-{$matches[4]}";
        }

        return $decrypted;
    }

    /**
     * Decrypts and returns the student's email.
     *
     * @param null|string $value Encrypted email
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
     * @param null|string $value Encrypted phone number
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

        if (preg_match('/^(\d{2})(\d{5})(\d{4})$/', $numbersOnly, $matches)) {
            return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
        }

        return $decrypted;
    }

    /**
     * Defines the relationship between the record and its corresponding student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Defines the relationship between the record and its corresponding school class.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Retrieves unique combinations of course, period, term, and borrowing permission
     * to be used as filter data.
     *
     * @param null|\Illuminate\Database\Eloquent\Builder $query Optional query builder instance
     *
     * @return \Illuminate\Support\Collection Filtered data collection
     */
    public static function getFilterData($query = null)
    {
        $query = $query ?? static::query();

        $query->getQuery()->orders = null;

        return $query->select('course', 'period', 'term', 'can_borrow')
            ->groupBy('course', 'period', 'term', 'can_borrow')
            ->orderBy('course')
            ->orderBy('period')
            ->orderBy('term')
            ->get()
        ;
    }
}
