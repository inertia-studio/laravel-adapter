<?php

namespace InertiaStudio\Columns;

class BooleanColumn extends BaseColumn
{
    protected string $type = 'boolean';

    protected ?string $trueIcon = null;

    protected ?string $falseIcon = null;

    protected ?string $trueColor = null;

    protected ?string $falseColor = null;

    public function trueIcon(string $icon): static
    {
        $this->trueIcon = $icon;

        return $this;
    }

    public function falseIcon(string $icon): static
    {
        $this->falseIcon = $icon;

        return $this;
    }

    public function trueColor(string $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function falseColor(string $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'trueIcon' => $this->trueIcon,
            'falseIcon' => $this->falseIcon,
            'trueColor' => $this->trueColor,
            'falseColor' => $this->falseColor,
        ];
    }
}
