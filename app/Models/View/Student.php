<?php

namespace App\Models\View;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends BaseModel
{
    protected $table = 'vw_students';
    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    public function getCpfAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
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

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'vw_student_school_class',
            'student_id',
            'school_class_id'
        );
    }
}
