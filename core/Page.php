<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

/**
 * @deprecated Use {@see \InertiaStudio\Pages\DashboardPage} to define custom panel pages.
 */
class Page implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected ?string $label = null;

    protected string $navigationPosition = 'after-list';

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function navigationPosition(string $position): static
    {
        $this->navigationPosition = $position;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->getIconSchema(),
            'navigationPosition' => $this->navigationPosition,
        ];
    }
}
