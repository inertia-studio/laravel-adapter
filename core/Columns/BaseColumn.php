<?php

namespace InertiaStudio\Columns;

use Closure;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasLabel;
use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Concerns\HasVisibility;
use JsonSerializable;

abstract class BaseColumn implements JsonSerializable
{
    use HasIcon;
    use HasLabel;
    use HasSchema;
    use HasVisibility;

    protected string $type;

    protected bool $isSortable = false;

    protected bool $isSearchable = false;

    protected bool $isToggleable = false;

    protected bool $isToggledHiddenByDefault = false;

    protected bool $isCopyable = false;

    protected ?Closure $url = null;

    protected string|Closure|null $description = null;

    protected ?Closure $getStateUsing = null;

    protected ?string $alignment = null;

    protected bool $canWrap = false;

    public function __construct(
        protected string $name,
    ) {}

    public function searchable(): static
    {
        $this->isSearchable = true;

        return $this;
    }

    public function sortable(): static
    {
        $this->isSortable = true;

        return $this;
    }

    public function toggleable(bool $isToggledHiddenByDefault = false): static
    {
        $this->isToggleable = true;
        $this->isToggledHiddenByDefault = $isToggledHiddenByDefault;

        return $this;
    }

    public function copyable(): static
    {
        $this->isCopyable = true;

        return $this;
    }

    public function url(Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function description(string|Closure $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStateUsing(Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function alignment(string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function wrap(): static
    {
        $this->canWrap = true;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->getLabel(),
            'sortable' => $this->isSortable,
            'searchable' => $this->isSearchable,
            'toggleable' => $this->isToggleable,
            'isToggledHiddenByDefault' => $this->isToggledHiddenByDefault,
            'copyable' => $this->isCopyable,
            'alignment' => $this->alignment,
            'wrap' => $this->canWrap,
            'hidden' => $this->isHidden(),
            'url' => $this->url !== null,
            'description' => $this->description instanceof Closure ? ($this->description)() : $this->description,
            'icon' => $this->getIconSchema(),
        ];
    }
}
