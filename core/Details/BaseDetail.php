<?php

namespace InertiaStudio\Details;

use Closure;
use InertiaStudio\Concerns\HasColumns;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasLabel;
use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Concerns\HasVisibility;
use JsonSerializable;

abstract class BaseDetail implements JsonSerializable
{
    use HasColumns;
    use HasIcon;
    use HasLabel;
    use HasSchema;
    use HasVisibility;

    protected string $type;

    protected bool $copyable = false;

    protected string|Closure|null $url = null;

    protected ?Closure $getStateUsing = null;

    public function __construct(
        protected string $name,
    ) {}

    public function copyable(bool $condition = true): static
    {
        $this->copyable = $condition;

        return $this;
    }

    public function url(string|Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getStateUsing(Closure $callback): static
    {
        $this->getStateUsing = $callback;

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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $url = $this->url instanceof Closure ? ($this->url)() : $this->url;

        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->getLabel(),
            'copyable' => $this->copyable,
            'icon' => $this->getIconSchema(),
            'url' => $url,
            'hidden' => $this->isHidden(),
            'columnSpan' => $this->getColumnSpan(),
        ];
    }
}
