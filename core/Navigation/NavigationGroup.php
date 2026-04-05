<?php

namespace InertiaStudio\Navigation;

use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class NavigationGroup implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected bool $collapsible = false;

    protected bool $collapsed = false;

    /** @var array<class-string> */
    protected array $items = [];

    public function __construct(
        protected string $label,
    ) {}

    public static function make(string $label): static
    {
        return new static($label);
    }

    /**
     * @param  array<class-string>  $items
     */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;
        $this->collapsible = true;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<class-string>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->getIconSchema(),
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'items' => [], // filled by NavigationBuilder
        ];
    }
}
