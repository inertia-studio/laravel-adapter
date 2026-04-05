<?php

namespace InertiaStudio\Filters;

class SelectFilter extends BaseFilter
{
    protected string $type = 'select';

    /** @var array<int|string, string> */
    protected array $options = [];

    protected bool $isSearchable = false;

    protected bool $isMultiple = false;

    /** @param array<int|string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function searchable(): static
    {
        $this->isSearchable = true;

        return $this;
    }

    public function multiple(): static
    {
        $this->isMultiple = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'options' => $this->options,
            'searchable' => $this->isSearchable,
            'multiple' => $this->isMultiple,
        ];
    }
}
