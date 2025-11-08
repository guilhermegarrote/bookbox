<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that the end date of a term is within the valid interval
 * based on the term type (Annual or Semester).
 */
class ValidTermInterval implements ValidationRule
{
    private string $start_date;
    private string $term;

    /**
     * Constructor.
     *
     * @param string $start_date Start date of the term (Y-m-d)
     * @param string $term Term type ('Annual' or 'Semester')
     */
    public function __construct(string $start_date, string $term)
    {
        $this->start_date = $start_date;
        $this->term = $term;
    }

    /**
     * Validate the given attribute.
     *
     * @param string $attribute The attribute name being validated
     * @param mixed $value The value of the attribute (end date)
     * @param \Closure $fail Closure to call on validation failure
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        try {
            $start = new \DateTime($this->start_date);
            $end = new \DateTime($value);
            $interval = $start->diff($end);

            $valid = match ($this->term) {
                'Annual' => $interval->y >= 1 && $interval->y <= 10,
                'Semester' => !($interval->y > 10 || ($interval->y === 10 && $interval->m > 0))
                    && ($interval->y > 0 || $interval->m >= 6),
                default => false,
            };

            if (!$valid) {
                $fail('A data de fim deve ter no mínimo um período completo e no máximo 10 anos de duração.');
            }
        } catch (\Exception) {
            $fail('A data de fim é inválida.');
        }
    }
}
