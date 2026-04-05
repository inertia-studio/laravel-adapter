<?php

namespace InertiaStudio\Fields;

class StepperField extends BaseField
{
    protected string $type = 'stepper';

    protected ?int $min = null;

    protected ?int $max = null;

    protected ?int $step = null;

    public function min(int $value): static
    {
        $this->min = $value;

        return $this;
    }

    public function max(int $value): static
    {
        $this->max = $value;

        return $this;
    }

    public function step(int $value): static
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
