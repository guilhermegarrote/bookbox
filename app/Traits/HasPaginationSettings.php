<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasPaginationSettings
{
    /**
     * Paginate query results dynamically.
     *
     * Uses the frontend-provided perPage value (from JS)
     * and cursor pagination for infinite scroll.
     */
    public function paginateWithSettings(Builder $query, Request $request, int $maxLimit = 250)
    {
        $perPage = max(5, min((int) $request->input('perPage', 15), $maxLimit));

        return $query->cursorPaginate($perPage)->withQueryString();
    }
}
