<?php

namespace InertiaStudio\Filters;

class TernaryFilter extends BaseFilter
{
    protected string $type = 'ternary';

    protected bool $isNullable = false;

    protected ?string $trueLabel = null;

    protected ?string $falseLabel = null;

    public function nullable(): static
    {
        $this->isNullable = true;

        return $this;
    }

    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'nullable' => $this->isNullable,
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
        ];
    }
}
