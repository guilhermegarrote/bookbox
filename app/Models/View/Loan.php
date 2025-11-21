<?php

declare(strict_types=1);

namespace App\Models\View;

use App\Helpers\Utils;
use App\Models\Copy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

/**
 * Class Loan.
 *
 * Represents a view model for book loans with decrypted and formatted attributes.
 * This model is mapped to the database view `vw_loans`, containing joined and derived data
 * from multiple related entities (students, copies, books, etc.).
 */
class Loan extends BaseModel
{
    /** @var bool Indicates if the model should be timestamped. */
    public $timestamps = false;

    /** @var string The database table (view) associated with the model. */
    protected $table = 'vw_loans';

    /** @var array<int, string> The attributes that aren’t mass assignable. */
    protected $guarded = [];

    /** @var array<int, string> The attributes that should be hidden in serialization. */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    /**
     * Converts binary UUID (bytes) to string.
     *
     * @param null|string $value
     *
     * @return null|string
     */
    public function getStudentIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts binary UUID (bytes) to string.
     *
     * @param null|string $value
     *
     * @return null|string
     */
    public function getBookIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts binary UUID (bytes) to string.
     *
     * @param null|string $value
     *
     * @return null|string
     */
    public function getCopyIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts binary UUID (bytes) to string.
     *
     * @param null|string $value
     *
     * @return null|string
     */
    public function getGenreIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Converts binary UUID (bytes) to string.
     *
     * @param null|string $value
     *
     * @return null|string
     */
    public function getSchoolClassIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Decrypts and formats CPF number.
     *
     * @param null|string $value
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
     * Decrypts email address.
     *
     * @param null|string $value
     */
    public function getEmailAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Decrypts and formats phone number.
     *
     * @param null|string $value
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
     * Formats ISBN number to a readable pattern.
     *
     * @param null|string $value
     */
    public function getIsbnAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $numbersOnly = preg_replace('/\D/', '', $value);

        if (\strlen($numbersOnly) === 13) {
            return \sprintf(
                '%s-%s-%s-%s-%s',
                substr($numbersOnly, 0, 3),
                substr($numbersOnly, 3, 1),
                substr($numbersOnly, 4, 4),
                substr($numbersOnly, 8, 4),
                substr($numbersOnly, 12, 1),
            );
        }

        return $numbersOnly;
    }

    /**
     * Formats the loan start date to "d/m/Y".
     *
     * @param null|string $value
     */
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

    /**
     * Formats the loan due date to "d/m/Y".
     *
     * @param null|string $value
     */
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

    /**
     * Defines the relationship between a loan and its student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Defines the relationship between a loan and its copy.
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Returns distinct values used for filtering loan data in the UI.
     *
     * @param null|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFilterData($query = null)
    {
        $query = ($query ?? static::query())->clone();

        return $query
            ->select(
                'genre_name',
                'publisher',
                'course',
                'period',
                'term',
                DB::raw('CASE WHEN loan_returned_date IS NULL THEN true ELSE false END as active'),
            )
            ->groupBy('genre_name', 'publisher', 'course', 'period', 'term', 'active')
            ->orderBy('genre_name')
            ->orderBy('publisher')
            ->orderBy('course')
            ->orderBy('period')
            ->orderBy('term')
            ->orderBy('active')
            ->distinct()
            ->get()
        ;
    }

    /**
     * Retrieves loan information required for rendering the sidebar,
     * including the title and the number of days until (or since) the due date.
     *
     * - Positive "days_diff"   → days until due date.
     * - Zero                   → due today.
     * - Negative "days_diff"   → overdue by N days.
     *
     * @param null|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getSidebarData($query = null)
    {
        $query = $query ? clone $query : static::query();

        return $query
            ->select([
                'id',
                'title',
                DB::raw('DATEDIFF(DATE(loan_due_date), CURDATE()) AS days_diff'),
            ])
            ->whereNull('loan_returned_date')
            ->get()
        ;
    }
}
