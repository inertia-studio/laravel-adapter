<?php

namespace InertiaStudio\Details;

class ImageDetail extends BaseDetail
{
    protected string $type = 'image';

    protected bool $circular = false;

    protected ?string $width = null;

    protected ?string $height = null;

    public function circular(bool $condition = true): static
    {
        $this->circular = $condition;

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
            'circular' => $this->circular,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
