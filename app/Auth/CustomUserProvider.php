<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;

/**
 * Custom user provider that supports binary UUID identifiers.
 *
 * @extends \Illuminate\Auth\EloquentUserProvider
 */
class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier the unique user identifier (UUID string or binary)
     *
     * @return null|Authenticatable the retrieved user instance or null if not found
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        if (\is_string($identifier)) {
            $identifier = $this->uuidStringToBytes($identifier);

            if ($identifier === null) {
                return null;
            }
        }

        $modelClass = $this->createModel();
        $model = new $modelClass();

        return $model->newQuery()->find($identifier);
    }

    /**
     * Convert a UUID string representation to its binary form.
     *
     * @param string $uuid the UUID in string format
     *
     * @throws InvalidUuidStringException if the UUID format is invalid (caught internally)
     *
     * @return null|string the binary representation of the UUID, or null if invalid
     */
    protected function uuidStringToBytes(string $uuid): ?string
    {
        if (!Uuid::isValid($uuid)) {
            return null;
        }

        try {
            return Uuid::fromString($uuid)->getBytes();
        } catch (InvalidUuidStringException) {
            return null;
        }
    }
}
