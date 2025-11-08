<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a key-value configuration setting in the system.
 *
 * Each setting is identified by a unique key and holds a corresponding value.
 */
class Setting extends BaseModel
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
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];
}
