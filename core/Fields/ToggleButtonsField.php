<?php

namespace InertiaStudio\Fields;

class ToggleButtonsField extends BaseField
{
    protected string $type = 'toggleButtons';

    /** @var array<int|string, string> */
    protected array $options = [];

    protected bool $multiple = false;

    protected bool $grouped = false;

    /** @param array<int|string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    public function grouped(): static
    {
        $this->grouped = true;

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
            'multiple' => $this->multiple,
            'grouped' => $this->grouped,
        ];
    }
}
