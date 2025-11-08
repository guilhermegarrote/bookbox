<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Ramsey\Uuid\Uuid;

/**
 * Class User.
 *
 * Represents a system user with authentication via JWT.
 * Handles encrypted email storage, password hashing, and relations with password reset codes.
 */
class User extends Authenticatable implements JWTSubject
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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
    protected $table = 'users';

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'binary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_hash',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email_hash',
        'password',
    ];

    /**
     * Get the UUID string of the user's ID.
     *
     * @param null|string $value Binary UUID
     *
     * @return null|string String UUID
     */
    public function getIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Encrypt and store the user's email, and store its hash for lookup.
     */
    public function setEmailAttribute(string $value): void
    {
        $email = Str::lower(trim($value));
        $this->attributes['email'] = Utils::encrypt($email);
        $this->attributes['email_hash'] = hash('sha256', $email, true);
    }

    /**
     * Decrypt the stored email.
     */
    public function getEmailAttribute(?string $value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    /**
     * Hash and store the password securely.
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make(trim($value));
    }

    /**
     * Relationship: User has many password reset codes.
     */
    public function passwordResetCodes(): HasMany
    {
        return $this->hasMany(PasswordResetCode::class, 'user_id');
    }

    /**
     * Get the identifier for JWT (primary key).
     *
     * @return null|string
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key-value array of custom JWT claims.
     *
     * @return array<string, string>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    /**
     * Get the password for authentication.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Boot method to generate UUID before creating a new user.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->getBytes();
            }
        });
    }
}
