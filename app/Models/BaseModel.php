<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Base model used to provide UUID (binary) primary keys and automatic conversion.
 *
 * This abstract model standardizes UUID handling for all Eloquent models that extend it.
 * - Uses binary UUIDs as primary keys in the database.
 * - Automatically converts UUIDs between binary (DB) and string (application).
 * - Automatically generates a UUID when creating new records.
 */
class BaseModel extends Model
{
    /**
     * Indicates that the model does not use auto-incrementing IDs.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'binary';

    /**
     * Converts the binary UUID stored in the database into a string UUID.
     *
     * @param mixed $value binary value from the database
     *
     * @return null|string UUID in string format, or null if not set
     */
    public function getIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }

    /**
     * Boot function for the model.
     *
     * This ensures that every new record automatically gets a UUID assigned
     * if no ID is provided during creation.
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
