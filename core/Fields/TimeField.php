<?php

namespace InertiaStudio\Fields;

class TimeField extends BaseField
{
    protected string $type = 'time';

    protected bool $seconds = false;

    protected ?string $format = null;

    public function seconds(): static
    {
        $this->seconds = true;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'seconds' => $this->seconds,
            'format' => $this->format,
        ];
    }
}
