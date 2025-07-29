<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Helpers\Utils;

class Student extends BaseModel
{
    protected $table = 'students';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'cpf',
        'cpf_hash',
        'email',
        'email_hash',
        'phone',
        'phone_hash',
    ];

    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    public function setCpfAttribute(string $value): void
    {
        $cpf = preg_replace('/\D/', '', trim($value));
        $this->attributes['cpf'] = Utils::encrypt($cpf);
        $this->attributes['cpf_hash'] = hash('sha256', $cpf, true);
    }

    public function setEmailAttribute(string $value): void
    {
        $email = Str::lower(trim($value));
        $this->attributes['email'] = Utils::encrypt($email);
        $this->attributes['email_hash'] = hash('sha256', $email, true);
    }

    public function setPhoneAttribute(string $value): void
    {
        $phone = preg_replace('/\D/', '', trim($value));
        $this->attributes['phone'] = Utils::encrypt($phone);
        $this->attributes['phone_hash'] = hash('sha256', $phone, true);
    }

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

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'student_school_class',
            'student_id',
            'school_class_id'
        );
    }
}
