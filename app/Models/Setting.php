<?php

namespace App\Models;

class Setting extends BaseModel
{
    protected $table = 'settings';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
    ];
}
