<?php

namespace InertiaStudio\Fields;

class UrlField extends BaseField
{
    protected string $type = 'url';

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
