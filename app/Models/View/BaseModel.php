<?php

declare(strict_types=1);

namespace App\Models\View;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Base model for database views (read-only).
 */
class BaseModel extends Model
{
    /**
     * Prevents saving, enforcing read-only behavior for view models.
     *
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        throw new \Exception('View is read-only.');
    }

    /**
     * Converts binary UUID (BLOB) to string representation.
     *
     * @param null|string $value
     */
    public function getIdAttribute($value): ?string
    {
        return $value ? Uuid::fromBytes($value)->toString() : null;
    }
}
