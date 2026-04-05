<?php

namespace InertiaStudio\Columns;

class IconColumn extends BaseColumn
{
    protected string $type = 'icon';

    /** @var array<string, string> */
    protected array $colors = [];

    protected ?string $size = null;

    /** @param array<string, string> $colors */
    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

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
            'size' => $this->size,
        ];
    }
}
