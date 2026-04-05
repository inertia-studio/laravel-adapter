<?php

namespace InertiaStudio\Filters;

use InertiaStudio\Concerns\HasLabel;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

abstract class BaseFilter implements JsonSerializable
{
    use HasLabel;
    use HasSchema;

    protected string $type;

    protected mixed $default = null;

    public function __construct(
        protected string $name,
    ) {}

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->getLabel(),
        ];
    }
}
