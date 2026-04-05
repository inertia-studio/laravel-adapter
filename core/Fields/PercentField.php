<?php

namespace InertiaStudio\Fields;

class PercentField extends BaseField
{
    protected string $type = 'percent';

    protected ?int $precision = null;

    public function precision(int $precision): static
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'precision' => $this->precision,
        ];
    }
}
