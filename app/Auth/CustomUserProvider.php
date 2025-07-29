<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\Uuid;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Recupera o usuário pelo ID.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        if (is_string($identifier)) {
            $identifier = $this->uuidStringToBytes($identifier);
        }

        $modelClass = $this->createModel();
        $model = new $modelClass;

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }


    /**
     * Converte UUID string para bytes binários.
     */
    protected function uuidStringToBytes(string $uuid): string
    {
        return Uuid::fromString($uuid)->getBytes();
    }
}
