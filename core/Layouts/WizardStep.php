<?php

namespace InertiaStudio\Layouts;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class WizardStep implements JsonSerializable
{
    use HasSchema;

    protected string $label;

    protected ?string $description = null;

    protected ?string $icon = null;

    /** @var array<int, mixed> */
    protected array $schema = [];

    protected int $columns = 1;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): static
    {
        return new static($label);
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'wizard-step',
            'label' => $this->label,
            'description' => $this->description,
            'icon' => $this->icon,
            'columns' => $this->columns,
            'schema' => array_map(
                fn (mixed $component) => $component->toArray(),
                $this->schema,
            ),
        ];
    }
}
