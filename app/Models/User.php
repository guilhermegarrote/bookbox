<?php

namespace App\Models;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    protected $table = 'users';
    public $incrementing = false;
    protected $keyType = 'binary';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'email_hash',
        'password',
    ];

    protected $hidden = [
        'email_hash',
        'password',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->getBytes();
            }
        });
    }

    public function getIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    public function setEmailAttribute(string $value): void
    {
        $email = Str::lower(trim($value));
        $this->attributes['email'] = Utils::encrypt($email);
        $this->attributes['email_hash'] = hash('sha256', $email, true);
    }

    public function getEmailAttribute(?string $value): ?string
    {
        return $value ? Utils::decrypt($value) : null;
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make(trim($value));
    }

    public function passwordResetCodes(): HasMany
    {
        return $this->hasMany(PasswordResetCode::class, 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->id;
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
