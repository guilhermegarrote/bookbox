<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasPaginationSettings
{
    /**
     * Paginate query results dynamically using cursor pagination.
     *
     * @param int $maxLimit Maximum items per page (default 250)
     */
    public function paginateWithSettings(Builder $query, Request $request, int $maxLimit = 250): \Illuminate\Pagination\CursorPaginator
    {
        $perPage = max(5, min((int) $request->input('perPage', 15), $maxLimit));

        return $query->cursorPaginate($perPage)->withQueryString();
    }
}
