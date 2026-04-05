<?php

namespace InertiaStudio\Fields;

class NumberField extends BaseField
{
    protected string $type = 'number';

    protected int|float|null $min = null;

    protected int|float|null $max = null;

    protected int|float|null $step = null;

    public function min(int|float $value): static
    {
        $this->min = $value;

        return $this;
    }

    public function max(int|float $value): static
    {
        $this->max = $value;

        return $this;
    }

    public function step(int|float $value): static
    {
        $this->step = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ];
    }
}
