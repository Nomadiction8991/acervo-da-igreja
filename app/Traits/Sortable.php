<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    protected function applySorting(Builder $query, Request $request, array $allowedColumns, string $defaultColumn = 'id'): Builder
    {
        $sortBy = $request->input('sort_by', $defaultColumn);
        $sortDir = $request->input('sort_dir', 'asc');

        // Validar para evitar SQL injection
        if (!in_array($sortBy, $allowedColumns, true)) {
            $sortBy = $defaultColumn;
        }

        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'asc';
        }

        return $query->orderBy($sortBy, $sortDir);
    }

    protected function getSortUrl(string $column, string $currentSort, string $currentDir): string
    {
        $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
        return request()->url() . '?' . http_build_query([
            ...request()->query(),
            'sort_by' => $column,
            'sort_dir' => $newDir,
        ]);
    }

    protected function getSortClass(string $column, string $currentSort, string $currentDir): string
    {
        if ($currentSort !== $column) {
            return '';
        }
        return $currentDir === 'asc' ? 'sort-asc' : 'sort-desc';
    }
}
