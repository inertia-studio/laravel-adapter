<?php

namespace InertiaStudio\Fields;

class ToggleField extends BaseField
{
    protected string $type = 'toggle';

    protected ?string $onIcon = null;

    protected ?string $offIcon = null;

    protected ?string $onColor = null;

    protected ?string $offColor = null;

    public function onIcon(string $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function offIcon(string $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onColor(string $color): static
    {
        $this->onColor = $color;

        return $this;
    }

    public function offColor(string $color): static
    {
        $this->offColor = $color;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'onIcon' => $this->onIcon,
            'offIcon' => $this->offIcon,
            'onColor' => $this->onColor,
            'offColor' => $this->offColor,
        ];
    }
}
