<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Tab implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    /** @var array<int, mixed> */
    protected array $schema = [];

    protected Closure|string|int|null $badge = null;

    protected ?Closure $tableResolver = null;

    protected ?string $relationClass = null;

    protected ?string $component = null;

    protected ?Closure $propsResolver = null;

    protected function __construct(
        protected string $label,
    ) {}

    public static function make(string $label): static
    {
        return new static($label);
    }

    /**
     * @param  Closure|array<int, mixed>  $components
     */
    public function schema(Closure|array $components): static
    {
        $this->schema = $components instanceof Closure ? $components() : $components;

        return $this;
    }

    public function badge(Closure|string|int $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function table(Closure $callback): static
    {
        $this->tableResolver = $callback;

        return $this;
    }

    public function relation(string $class): static
    {
        $this->relationClass = $class;

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
        $badge = $this->badge instanceof Closure ? ($this->badge)() : $this->badge;

        return [
            'label' => $this->label,
            'icon' => $this->getIconSchema(),
            'badge' => $badge,
            'slug' => str($this->label)->kebab()->toString(),
        ];
    }
}
