<?php

namespace InertiaStudio\Navigation;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class NavigationItem implements JsonSerializable
{
    use HasSchema;

    public function __construct(
        protected string $label,
        protected ?array $icon,
        protected string $url,
        protected string|int|null $badge = null,
        protected string $badgeColor = 'info',
        protected bool $isActive = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'url' => $this->url,
            'badge' => $this->badge,
            'badgeColor' => $this->badgeColor,
            'isActive' => $this->isActive,
        ];
    }
}
