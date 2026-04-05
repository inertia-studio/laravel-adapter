<?php

namespace InertiaStudio\Fields;

class DateField extends BaseField
{
    protected string $type = 'date';

    protected bool $withTime = false;

    protected ?string $format = null;

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    public function withTime(): static
    {
        $this->withTime = true;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function minDate(string $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    public function maxDate(string $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'withTime' => $this->withTime,
            'format' => $this->format,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
        ];
    }
}
