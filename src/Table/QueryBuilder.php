<?php

namespace InertiaStudio\Laravel\Table;

use Illuminate\Database\Eloquent\Builder;
use InertiaStudio\Filters\QueryFilter;
use InertiaStudio\Table;

class QueryBuilder
{
    public static function apply(
        Builder $query,
        Table $table,
        array $request,
    ): Builder {
        $query = static::applySearch($query, $table, $request['search'] ?? null);
        $query = static::applyFilters($query, $table, $request['filters'] ?? []);
        $query = static::applySort($query, $table, $request);

        return $query;
    }

    protected static function applySearch(Builder $query, Table $table, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        $searchableColumns = collect($table->getColumns())
            ->filter(fn ($col) => $col->isSearchable())
            ->map(fn ($col) => $col->getName())
            ->toArray();

        if (empty($searchableColumns)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($searchableColumns, $search) {
            foreach ($searchableColumns as $column) {
                if (str_contains($column, '.')) {
                    // Relationship column: "user.name" → whereHas('user', fn($q) => $q->where('name', ...))
                    $parts = explode('.', $column);
                    $field = array_pop($parts);
                    $relation = implode('.', $parts);
                    $q->orWhereHas($relation, fn (Builder $sub) => $sub->where($field, 'LIKE', "%{$search}%"));
                } else {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    protected static function applyFilters(Builder $query, Table $table, array $activeFilters): Builder
    {
        $filterDefinitions = $table->getFilters();

        foreach ($filterDefinitions as $filter) {
            $name = $filter->getName();
            $value = $activeFilters[$name] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            // QueryFilter with custom callback always takes priority
            if ($filter instanceof QueryFilter && $filter->getQuery()) {
                $filter->applyQuery($query, $value);
            } else {
                match ($filter->getType()) {
                    'select' => $query->whereIn($name, is_array($value) ? $value : [$value]),
                    'boolean' => $query->where($name, (bool) $value),
                    'ternary' => match ($value) {
                        'true', '1', true => $query->whereNotNull($name),
                        'false', '0', false => $query->whereNull($name),
                        default => $query,
                    },
                    'date' => static::applyDateFilter($query, $name, $value),
                    default => $query,
                };
            }
        }

        return $query;
    }

    protected static function applyDateFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (is_array($value)) {
            if (! empty($value['from'])) {
                $query->where($column, '>=', $value['from'].' 00:00:00');
            }
            if (! empty($value['to'])) {
                $query->where($column, '<=', $value['to'].' 23:59:59');
            }
        }

        return $query;
    }

    protected static function applySort(Builder $query, Table $table, array $request): Builder
    {
        $sortColumn = $request['sort'] ?? null;
        $sortDirection = $request['direction'] ?? 'asc';

        if ($sortColumn) {
            $sortableColumns = collect($table->getColumns())
                ->filter(fn ($col) => $col->isSortable())
                ->map(fn ($col) => $col->getName())
                ->toArray();

            if (in_array($sortColumn, $sortableColumns)) {
                if (str_contains($sortColumn, '.')) {
                    // Relationship sort: use a subquery
                    $parts = explode('.', $sortColumn);
                    $field = array_pop($parts);
                    $relation = implode('.', $parts);
                    $related = $query->getModel()->{$relation}()->getRelated();

                    return $query->orderBy(
                        $related->newQuery()
                            ->select($field)
                            ->whereColumn($related->getQualifiedKeyName(), $query->getModel()->getTable().'.'.$relation.'_id')
                            ->limit(1),
                        $sortDirection,
                    );
                }

                return $query->orderBy($sortColumn, $sortDirection);
            }
        }

        $defaultSort = $table->getDefaultSort();
        if ($defaultSort) {
            return $query->orderBy($defaultSort['column'], $defaultSort['direction']);
        }

        return $query;
    }
}
