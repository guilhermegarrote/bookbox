<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class PasswordResetCode extends BaseModel
{
    protected $table = 'password_reset_codes';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'value',
        'expiration',
    ];

    public function setValueAttribute(string $value): void
    {
        $this->attributes['value'] = Hash::make(trim($value));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
