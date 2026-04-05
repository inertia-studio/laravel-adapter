<?php

namespace InertiaStudio\Fields;

class SelectField extends BaseField
{
    protected string $type = 'select';

    /** @var array<int|string, string> */
    protected array $options = [];

    protected bool $searchable = false;

    protected bool $multiple = false;

    protected bool $preload = false;

    protected bool $native = false;

    /** @param array<int|string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    public function preload(): static
    {
        $this->preload = true;

        return $this;
    }

    public function native(): static
    {
        $this->native = true;

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
            'searchable' => $this->searchable,
            'multiple' => $this->multiple,
            'preload' => $this->preload,
            'native' => $this->native,
        ];
    }
}
