<?php

namespace App\Rules;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTermInterval implements ValidationRule
{
    private string $start_date;
    private string $term;

    public function __construct(string $start_date, string $term)
    {
        $this->start_date = $start_date;
        $this->term = $term;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $start = new DateTime($this->start_date);
            $end = new DateTime($value);
            $interval = $start->diff($end);

            $valid = match ($this->term) {
                'Annual' => $interval->y >= 1 && $interval->y <= 10,
                'Semester' => !($interval->y > 10 || ($interval->y === 10 && $interval->m > 0)) &&
                    ($interval->y > 0 || $interval->m >= 6),
                default => false
            };

            if (! $valid) {
                $fail('A data de fim deve ter no mínimo um período completo e no máximo 10 anos de duração.');
            }
        } catch (\Exception) {
            $fail('A data de fim é inválida.');
        }
    }
}
