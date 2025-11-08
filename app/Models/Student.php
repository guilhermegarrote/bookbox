<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Represents a student with encrypted personal information (CPF, email, phone).
 * Provides relationships to loans and school classes.
 */
class Student extends BaseModel
{
    /**
     * Indicates if the model should not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'cpf_hash',
        'email',
        'email_hash',
        'phone',
        'phone_hash',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'cpf_hash',
        'email_hash',
        'phone_hash',
    ];

    /**
     * Sets and encrypts the CPF, also stores its SHA-256 hash.
     *
     * @param string $value Plain CPF
     */
    public function setCpfAttribute(string $value): void
    {
        $cpf = preg_replace('/\D/', '', trim($value));
        $this->attributes['cpf'] = Utils::encrypt($cpf);
        $this->attributes['cpf_hash'] = hash('sha256', $cpf, true);
    }

    /**
     * Sets and encrypts the email, also stores its SHA-256 hash.
     *
     * @param string $value Plain email
     */
    public function setEmailAttribute(string $value): void
    {
        $email = Str::lower(trim($value));
        $this->attributes['email'] = Utils::encrypt($email);
        $this->attributes['email_hash'] = hash('sha256', $email, true);
    }

    /**
     * Sets and encrypts the phone number, also stores its SHA-256 hash.
     *
     * @param string $value Plain phone
     */
    public function setPhoneAttribute(string $value): void
    {
        $phone = preg_replace('/\D/', '', trim($value));
        $this->attributes['phone'] = Utils::encrypt($phone);
        $this->attributes['phone_hash'] = hash('sha256', $phone, true);
    }

    /**
     * Gets the decrypted CPF.
     *
     * @param null|string $value Encrypted CPF
     *
     * @return null|string Decrypted CPF
     */
    public function getCpfAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Gets the decrypted email.
     *
     * @param null|string $value Encrypted email
     *
     * @return null|string Decrypted email
     */
    public function getEmailAttribute($value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Gets the decrypted phone number, formatted if possible.
     *
     * @param null|string $value Encrypted phone
     *
     * @return null|string Formatted phone
     */
    public function getPhoneAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        $decrypted = Utils::decrypt($value);
        $numbersOnly = preg_replace('/\D/', '', $decrypted);

        if (\strlen($numbersOnly) === 11) {
            return \sprintf(
                '(%s) %s-%s',
                substr($numbersOnly, 0, 2),
                substr($numbersOnly, 2, 5),
                substr($numbersOnly, 7),
            );
        }

        return $decrypted;
    }

    /**
     * Relationship: Student has many loans.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relationship: Student belongs to many school classes.
     */
    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'student_school_class',
            'student_id',
            'school_class_id',
        );
    }
}
