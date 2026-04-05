<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Table implements JsonSerializable
{
    use HasSchema;

    /** @var array<int, mixed> */
    protected array $columns = [];

    /** @var array<int, mixed> */
    protected array $filters = [];

    /** @var array<int, mixed> */
    protected array $actions = [];

    /** @var array<int, mixed> */
    protected array $bulkActions = [];

    protected bool $searchable = true;

    protected bool $paginated = true;

    protected int $filterColumns = 2;

    /** Poll interval in seconds (0 = disabled) */
    protected int $poll = 0;

    /** @var array{column: string, direction: string}|null */
    protected ?array $defaultSort = null;

    protected ?Closure $query = null;

    /**
     * @param  array<int, mixed>  $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param  array<int, mixed>  $filters
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Number of columns in the filter grid (1–6).
     */
    /**
     * Enable live polling on the table. Records refresh automatically.
     *
     * @param  int  $seconds  Polling interval (e.g. 5 = every 5 seconds)
     */
    public function poll(int $seconds = 5): static
    {
        $this->poll = max(1, $seconds);

        return $this;
    }

    public function filterColumns(int $columns): static
    {
        $this->filterColumns = max(1, min(6, $columns));

        return $this;
    }

    /**
     * @param  array<int, mixed>  $actions
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param  array<int, mixed>  $bulkActions
     */
    public function bulkActions(array $bulkActions): static
    {
        $this->bulkActions = $bulkActions;

        return $this;
    }

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSort = [
            'column' => $column,
            'direction' => $direction,
        ];

        return $this;
    }

    public function searchable(bool $condition = true): static
    {
        $this->searchable = $condition;

        return $this;
    }

    public function paginated(bool $condition = true): static
    {
        $this->paginated = $condition;

        return $this;
    }

    public function query(Closure $callback): static
    {
        $this->query = $callback;

        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<int, mixed>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array{column: string, direction: string}|null
     */
    public function getDefaultSort(): ?array
    {
        return $this->defaultSort;
    }

    public function getQuery(): ?Closure
    {
        return $this->query;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSchema(): array
    {
        return [
            'type' => 'table',
            'columns' => array_map(
                fn (mixed $column) => $column->toArray(),
                $this->columns,
            ),
            'filters' => array_map(
                fn (mixed $filter) => $filter->toArray(),
                $this->filters,
            ),
            'actions' => array_map(
                fn (mixed $action) => $action->toArray(),
                $this->actions,
            ),
            'bulkActions' => array_map(
                fn (mixed $action) => $action->toArray(),
                $this->bulkActions,
            ),
            'searchable' => $this->searchable,
            'paginated' => $this->paginated,
            'defaultSort' => $this->defaultSort,
            'filterColumns' => $this->filterColumns,
            'poll' => $this->poll,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->toSchema();
    }
}
