<?php

namespace InertiaStudio\Columns;

class ImageColumn extends BaseColumn
{
    protected string $type = 'image';

    protected bool $isCircular = false;

    protected bool $isSquare = false;

    protected ?string $width = null;

    protected ?string $height = null;

    public function circular(): static
    {
        $this->isCircular = true;
        $this->isSquare = false;

        return $this;
    }

    public function square(): static
    {
        $this->isSquare = true;
        $this->isCircular = false;

        return $this;
    }

    public function width(string $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(string $height): static
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'circular' => $this->isCircular,
            'square' => $this->isSquare,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
