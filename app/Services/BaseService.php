<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    /**
     * Dynamic Seek / Cursor-based Pagination Helper
     * 
     * @param Builder|Relation $query
     * @param int $defaultLimit
     * @param string $cursorColumn (Default 'id')
     * @param string $direction ('desc' or 'asc')
     * @return array
     */
    protected function applySeekPagination(Builder|Relation $query, int $defaultLimit = 12, string $cursorColumn = 'id', string $direction = 'desc'): array
    {
        // Request query theke limit ebong last_id fetch kora
        $limit = request()->integer('limit', $defaultLimit);
        $limit = min(max($limit, 1), 100); // Safety limit (max 100)

        $lastId = request()->get('last_id'); // e.g. ?last_id=45&limit=18

        // Conditional Where condition dynamically applied
        $operator = strtolower($direction) === 'desc' ? '<' : '>';

        $items = $query->when($lastId, function ($q) use ($cursorColumn, $operator, $lastId) {
            $q->where($cursorColumn, $operator, $lastId);
        })->orderBy($cursorColumn, $direction)
            ->take($limit + 1) // Next page (has_more) checking-er jonno 1-ta item extra fetch kora
            ->get();

        // Check kora hocche porer page-e data aache kina
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items->pop(); // Extra item-ti array/collection theke remove kora
        }

        // Response shape matching
        return [
            'items'     => $items,
            'has_more'  => $hasMore,
            'last_id'   => $items->last()?->{$cursorColumn}, // Agami request-e ei last_id frontend pathabe
            'count'     => $items->count(),
        ];
    }

    protected function applyPagination(Builder|Relation $query, int $defaultPerPage = 12): LengthAwarePaginator
    {
        $perPage = request()->integer('per_page', $defaultPerPage);
        $perPage = min(max($perPage, 1), 100);
        return $query->paginate($perPage);
    }
}
