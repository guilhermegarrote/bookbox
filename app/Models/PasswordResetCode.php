<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

/**
 * Represents a password reset code associated with a user.
 *
 * The code is hashed before saving and has an expiration date.
 */
class PasswordResetCode extends BaseModel
{
    /**
     * Indicates if the model should not use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table used by this model.
     *
     * @var string
     */
    protected $table = 'password_reset_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'value',
        'expiration',
    ];

    /**
     * Hashes the value before saving it to the database.
     *
     * @param string $value the plain text reset code
     */
    public function setValueAttribute(string $value): void
    {
        $this->attributes['value'] = Hash::make(trim($value));
    }

    /**
     * Defines the relationship to the user who owns this reset code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
