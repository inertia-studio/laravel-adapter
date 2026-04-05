<?php

namespace InertiaStudio\Concerns;

trait HasLabel
{
    protected ?string $label = null;

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        if ($this->label !== null) {
            return $this->label;
        }

        return str($this->name)
            ->afterLast('.')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }
}
