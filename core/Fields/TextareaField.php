<?php

namespace InertiaStudio\Fields;

class TextareaField extends BaseField
{
    protected string $type = 'textarea';

    protected ?int $rows = null;

    protected bool $autosize = false;

    protected ?int $maxLength = null;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function autosize(): static
    {
        $this->autosize = true;

        return $this;
    }

    public function maxLength(int $maxLength): static
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'rows' => $this->rows,
            'autosize' => $this->autosize,
            'maxLength' => $this->maxLength,
        ];
    }
}
