<?php

namespace InertiaStudio\Concerns;

use Closure;

trait HasState
{
    protected mixed $default = null;

    protected bool|Closure $disabled = false;

    protected bool|Closure $required = false;

    protected bool $reactive = false;

    protected ?Closure $dehydrateStateUsing = null;

    protected bool|Closure $dehydrated = true;

    protected ?Closure $afterStateUpdated = null;

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function disabled(bool|Closure $condition = true): static
    {
        $this->disabled = $condition;

        return $this;
    }

    public function required(bool|Closure $condition = true): static
    {
        $this->required = $condition;

        return $this;
    }

    public function reactive(): static
    {
        $this->reactive = true;

        return $this;
    }

    public function dehydrateStateUsing(Closure $callback): static
    {
        $this->dehydrateStateUsing = $callback;

        return $this;
    }

    public function dehydrated(bool|Closure $condition = true): static
    {
        $this->dehydrated = $condition;

        return $this;
    }

    public function afterStateUpdated(Closure $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled instanceof Closure ? ($this->disabled)() : $this->disabled;
    }

    public function isRequired(): bool
    {
        return $this->required instanceof Closure ? ($this->required)() : $this->required;
    }
}
