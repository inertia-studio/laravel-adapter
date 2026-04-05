<?php

namespace InertiaStudio\Concerns;

trait HasColumns
{
    protected int|string $columnSpan = 1;

    public function columnSpan(int|string $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function getColumnSpan(): int|string
    {
        return $this->columnSpan;
    }
}
