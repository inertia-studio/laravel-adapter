<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Page implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected ?string $label = null;

    protected string $navigationPosition = 'after-list';

    protected ?string $component = null;

    protected ?Closure $propsResolver = null;

    protected function __construct(
        protected string $slug,
    ) {}

    public static function make(string $slug): static
    {
        return new static($slug);
    }

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

    public function component(string $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function props(Closure $callback): static
    {
        $this->propsResolver = $callback;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'label' => $this->label,
            'icon' => $this->getIconSchema(),
            'navigationPosition' => $this->navigationPosition,
        ];
    }
}
