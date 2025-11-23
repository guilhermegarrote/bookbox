<?php

declare(strict_types=1);

namespace App\Models\View;

use App\Helpers\Utils;
use App\Models\Copy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

/**
 * Class Loan
 *
 * Represents a view model for book loans with decrypted and formatted attributes.
 * This model maps to the database view `vw_loans` and includes joined/derived data
 * from students, copies, books, genres, and related entities.
 *
 * @package App\Models\View
 */
class Loan extends BaseModel
{
    /** @var bool Indicates if the model should be timestamped (disabled). */
    public $timestamps = false;

    /** @var string The database table (view) associated with the model. */
    protected $table = 'vw_loans';

    /** @var array<int, string> The attributes that aren’t mass assignable. */
    protected $guarded = [];

    /** @var array<int, string> The attributes that should be hidden when serializing the model. */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    /**
     * Get student UUID as string from binary.
     *
     * @param string|null $value Binary UUID from the database.
     * @return string|null UUID as string or null if value is null.
     */
    public function getStudentIdAttribute(?string $value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Get book UUID as string from binary.
     *
     * @param string|null $value Binary UUID from the database.
     * @return string|null UUID as string or null if value is null.
     */
    public function getBookIdAttribute(?string $value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Get copy UUID as string from binary.
     *
     * @param string|null $value Binary UUID from the database.
     * @return string|null UUID as string or null if value is null.
     */
    public function getCopyIdAttribute(?string $value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Get genre UUID as string from binary.
     *
     * @param string|null $value Binary UUID from the database.
     * @return string|null UUID as string or null if value is null.
     */
    public function getGenreIdAttribute(?string $value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Get school class UUID as string from binary.
     *
     * @param string|null $value Binary UUID from the database.
     * @return string|null UUID as string or null if value is null.
     */
    public function getSchoolClassIdAttribute(?string $value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Decrypts and formats CPF number as XXX.XXX.XXX-XX.
     *
     * @param string|null $value Encrypted CPF from the database.
     * @return string|null Formatted CPF or null if value is null.
     */
    public function getCpfAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $decrypted = Utils::decrypt($value);
        $numbersOnly = preg_replace('/\D/', '', $decrypted);

        if (preg_match('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', $numbersOnly, $m)) {
            return "{$m[1]}.{$m[2]}.{$m[3]}-{$m[4]}";
        }

        return $decrypted;
    }

    /**
     * Decrypts email address.
     *
     * @param string|null $value Encrypted email from the database.
     * @return string|null Decrypted email or null if value is null.
     */
    public function getEmailAttribute(?string $value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Decrypts and formats phone number as (XX) XXXXX-XXXX.
     *
     * @param string|null $value Encrypted phone from the database.
     * @return string|null Formatted phone or decrypted raw value if pattern doesn't match.
     */
    public function getPhoneAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $decrypted = Utils::decrypt($value);
        $numbersOnly = preg_replace('/\D/', '', $decrypted);

        if (preg_match('/^(\d{2})(\d{5})(\d{4})$/', $numbersOnly, $m)) {
            return "({$m[1]}) {$m[2]}-{$m[3]}";
        }

        return $decrypted;
    }

    /**
     * Formats ISBN-13 number as XXX-X-XXXX-XXXX-X.
     *
     * @param string|null $value Raw ISBN value.
     * @return string|null Formatted ISBN or numbers-only string if not 13 digits.
     */
    public function getIsbnAttribute(?string $value): ?string
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
                substr($numbersOnly, 12, 1),
            );
        }

        return $numbersOnly;
    }

    /**
     * Defines the relationship between a loan and its student.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Defines the relationship between a loan and a copy.
     *
     * @return BelongsTo
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Retrieves distinct values for filtering loan data.
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query Optional query builder instance.
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

    /**
     * Retrieves lightweight loan info for sidebar rendering.
     *
     * If a custom query is provided:
     *   - If it returns results, use them.
     *   - If it returns no results, fallback to the default dataset.
     *
     * @param \Illuminate\Database\Eloquent\Builder|null $query Optional query builder instance.
     * @return \Illuminate\Support\Collection
     */
    public static function getSidebarData($query = null)
    {
        $selectColumns = [
            'id',
            'title',
            DB::raw('DATEDIFF(DATE(loan_due_date), CURDATE()) AS days_diff'),
        ];

        if ($query) {
            $customResults = (clone $query)
                ->select($selectColumns)
                ->whereNull('loan_returned_date')
                ->get();

            if ($customResults->isNotEmpty()) {
                return $customResults;
            }
        }

        return static::query()
            ->select($selectColumns)
            ->whereNull('loan_returned_date')
            ->get();
    }
}
