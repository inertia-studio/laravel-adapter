<?php

namespace InertiaStudio\Fields;

class RatingField extends BaseField
{
    protected string $type = 'rating';

    protected int $max = 5;

    protected bool $allowHalf = false;

    protected string $ratingIcon = 'star';

    protected ?string $activeColor = null;

    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function allowHalf(bool $allow = true): static
    {
        $this->allowHalf = $allow;

        return $this;
    }

    public function ratingIcon(string $icon): static
    {
        $this->ratingIcon = $icon;

        return $this;
    }

    public function activeColor(string $color): static
    {
        $this->activeColor = $color;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'max' => $this->max,
            'allowHalf' => $this->allowHalf,
            'ratingIcon' => $this->ratingIcon,
            'activeColor' => $this->activeColor,
        ];
    }
}
