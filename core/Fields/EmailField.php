<?php

namespace InertiaStudio\Fields;

class EmailField extends BaseField
{
    protected string $type = 'email';

    protected ?int $maxLength = null;

    public function max(int $length): static
    {
        $this->maxLength = $length;

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
        ];
    }
}
