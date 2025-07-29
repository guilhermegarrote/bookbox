<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BaseModel extends Model
{
    public $incrementing = false;
    protected $keyType = 'binary';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->getBytes();
            }
        });
    }

    public function getIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }
}
