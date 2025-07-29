<?php

namespace App\Models\View;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BaseModel extends Model
{
    public function save(array $options = [])
    {
        throw new Exception("View is read-only.");
    }

    public function getIdAttribute($value)
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }
}
