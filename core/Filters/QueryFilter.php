<?php

namespace InertiaStudio\Filters;

use Closure;

class QueryFilter extends BaseFilter
{
    protected string $type = 'query';

    protected ?Closure $query = null;

    /** @var array<string, string> */
    protected array $options = [];

    protected bool $searchable = false;

    protected bool $multiple = false;

    protected ?string $relationshipName = null;

    protected ?string $relationshipTitleColumn = null;

    protected ?string $relationshipModel = null;

    protected bool $preload = false;

    public function query(Closure $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param  array<string, string>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Load options from a relationship.
     * The select will search server-side when combined with ->searchable().
     */
    public function relationship(string $name, string $titleColumn): static
    {
        $this->relationshipName = $name;
        $this->relationshipTitleColumn = $titleColumn;

        return $this;
    }

    /**
     * Preload all options on page load instead of waiting for search.
     * Only use for small datasets.
     */
    public function preload(bool $preload = true): static
    {
        $this->preload = $preload;

        return $this;
    }

    public function getRelationshipName(): ?string
    {
        return $this->relationshipName;
    }

    public function getRelationshipTitleColumn(): ?string
    {
        return $this->relationshipTitleColumn;
    }

    public function isPreload(): bool
    {
        return $this->preload;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function getQuery(): ?Closure
    {
        return $this->query;
    }

    public function applyQuery(mixed $builder, mixed $value = null): mixed
    {
        if ($this->query) {
            ($this->query)($builder, $value);
        }

        return $builder;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        // Render as select UI when options or relationship are set
        if (! empty($this->options) || $this->relationshipName) {
            $data['type'] = 'select';
            $data['options'] = $this->options;
            $data['searchable'] = $this->searchable;
            $data['multiple'] = $this->multiple;

            if ($this->relationshipName) {
                $data['serverSearch'] = true;
                $data['relationship'] = $this->relationshipName;
                $data['preload'] = $this->preload;
            }
        }

        return $data;
    }
}
