<?php

namespace InertiaStudio\Columns;

class DateColumn extends BaseColumn
{
    protected string $type = 'date';

    protected bool $isDateTime = false;

    protected bool $isSince = false;

    protected ?string $format = null;

    protected ?string $timezone = null;

    public function dateTime(): static
    {
        $this->isDateTime = true;

        return $this;
    }

    public function since(): static
    {
        $this->isSince = true;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function timezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'dateTime' => $this->isDateTime,
            'since' => $this->isSince,
            'format' => $this->format,
            'timezone' => $this->timezone,
        ];
    }
}
