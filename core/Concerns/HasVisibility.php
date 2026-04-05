<?php

namespace InertiaStudio\Concerns;

use Closure;

trait HasVisibility
{
    protected bool|Closure $visible = true;

    protected bool|Closure $hidden = false;

    protected bool $onlyOnCreate = false;

    protected bool $onlyOnEdit = false;

    public function visible(bool|Closure $condition = true): static
    {
        $this->visible = $condition;

        return $this;
    }

    public function hidden(bool|Closure $condition = true): static
    {
        $this->hidden = $condition;

        return $this;
    }

    public function onlyOnCreate(): static
    {
        $this->onlyOnCreate = true;

        return $this;
    }

    public function onlyOnEdit(): static
    {
        $this->onlyOnEdit = true;

        return $this;
    }

    public function isHidden(string $operation = 'create'): bool
    {
        if ($this->onlyOnCreate && $operation !== 'create') {
            return true;
        }

        if ($this->onlyOnEdit && $operation !== 'edit') {
            return true;
        }

        $hidden = $this->hidden instanceof Closure ? ($this->hidden)() : $this->hidden;
        $visible = $this->visible instanceof Closure ? ($this->visible)() : $this->visible;

        return $hidden || ! $visible;
    }
}
