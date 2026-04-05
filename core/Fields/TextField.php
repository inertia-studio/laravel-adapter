<?php

namespace InertiaStudio\Fields;

class TextField extends BaseField
{
    protected string $type = 'text';

    protected ?int $maxLength = null;

    protected ?int $minLength = null;

    public function max(int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function min(int $length): static
    {
        $this->minLength = $length;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'maxLength' => $this->maxLength,
            'minLength' => $this->minLength,
        ];
    }
}
