<?php

namespace InertiaStudio\Details;

class BadgeDetail extends BaseDetail
{
    protected string $type = 'badge';

    /** @var array<string, string> */
    protected array $colors = [];

    /**
     * @param  array<string, string>  $colors
     */
    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'colors' => $this->colors,
        ];
    }
}
