<?php

namespace App\Models\View;

use App\Helpers\Utils;
use Ramsey\Uuid\Uuid;

class StudentSchoolClass extends BaseModel
{
    protected $table = 'vw_student_school_class';
    public $timestamps = false;

    protected $guarded = [];

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
        return $value ? Utils::decrypt($value) : null;
    }

    public function getFormattedCpfAttribute()
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        if (strlen($cpf) !== 11) return $this->cpf;

        return substr($cpf, 0, 3) . '.' .
            substr($cpf, 3, 3) . '.' .
            substr($cpf, 6, 3) . '-' .
            substr($cpf, 9, 2);
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

        if (strlen($numbersOnly) === 11) {
            return sprintf(
                '(%s) %s-%s',
                substr($numbersOnly, 0, 2),
                substr($numbersOnly, 2, 5),
                substr($numbersOnly, 7)
            );
        }

        return $decrypted;
    }

    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/\D/', '', $this->phone);

        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' .
                substr($phone, 2, 5) . '-' .
                substr($phone, 7, 4);
        } else {
            return $this->phone;
        }
    }
}
