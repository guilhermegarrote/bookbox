<?php

namespace App\Models\View;

use App\Helpers\Utils;
use App\Models\Copy;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class Loan extends BaseModel
{
    protected $table = 'vw_loans';
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

    public function getBookIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getCopyIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function getGenreIdAttribute($value)
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

    public function getIsbnAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $numbersOnly = preg_replace('/\D/', '', $value);

        if (strlen($numbersOnly) === 13) {
            return sprintf(
                '%s-%s-%s-%s-%s',
                substr($numbersOnly, 0, 3),
                substr($numbersOnly, 3, 1),
                substr($numbersOnly, 4, 4),
                substr($numbersOnly, 8, 4),
                substr($numbersOnly, 12, 1)
            );
        }

        return $numbersOnly;
    }

    public function getLoanStartDateAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getLoanDueDateAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Returns data for filters (genre_name, publisher, course, period, term, active)
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @return \Illuminate\Support\Collection
     */
    public static function getFilterData($query = null)
    {
        $query = $query ?? static::query();

        return $query->select(
            'genre_name',
            'publisher',
            'course',
            'period',
            'term',
            DB::raw('CASE WHEN loan_returned_date IS NULL THEN true ELSE false END as active')
        )
            ->groupBy('genre_name', 'publisher', 'course', 'period', 'term', 'active')
            ->orderBy('genre_name')
            ->orderBy('publisher')
            ->orderBy('course')
            ->orderBy('period')
            ->orderBy('term')
            ->orderBy('active')
            ->get();
    }
}
