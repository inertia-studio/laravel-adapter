<?php

namespace InertiaStudio\Layouts;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Section implements JsonSerializable
{
    use HasSchema;

    /** @var array<int, mixed> */
    protected array $schema = [];

    protected int $columns = 1;

    protected bool $collapsible = false;

    protected bool $collapsed = false;

    protected bool $aside = false;

    protected ?string $description = null;

    public function __construct(
        protected string $heading,
    ) {}

    /**
     * @param  array<int, mixed>  $components
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function collapsible(bool $condition = true): static
    {
        $this->collapsible = $condition;

        return $this;
    }

    public function collapsed(bool $condition = true): static
    {
        $this->collapsed = $condition;

        if ($condition) {
            $this->collapsible = true;
        }

        return $this;
    }

    public function aside(bool $condition = true): static
    {
        $this->aside = $condition;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'section',
            'heading' => $this->heading,
            'description' => $this->description,
            'columns' => $this->columns,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'aside' => $this->aside,
            'schema' => array_map(
                fn (mixed $component) => $component->toArray(),
                $this->schema,
            ),
        ];
    }
}
