<?php

namespace InertiaStudio\Details;

class DateDetail extends BaseDetail
{
    protected string $type = 'date';

    protected ?string $format = null;

    protected bool $since = false;

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function since(bool $condition = true): static
    {
        $this->since = $condition;

        return $this;
    }

    public function dateTime(): static
    {
        $this->format = 'datetime';

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'format' => $this->format,
            'since' => $this->since,
        ];
    }
}
