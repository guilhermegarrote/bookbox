<?php

namespace App\Models\View;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class StudentSchoolClass extends BaseModel
{
    protected $table = 'vw_student_school_class';
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getSchoolClassIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

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

    public function getEmailAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Returns data for filters (course, period, term, can_borrow)
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @return \Illuminate\Support\Collection
     */
    public static function getFilterData($query = null)
    {
        $query = $query ?? static::query();

        return $query->select('course', 'period', 'term', 'can_borrow')
            ->groupBy('course', 'period', 'term', 'can_borrow')
            ->orderBy('course')
            ->orderBy('period')
            ->orderBy('term')
            ->get();
    }
}
