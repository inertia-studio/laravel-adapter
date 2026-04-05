<?php

namespace InertiaStudio\Concerns;

trait HasSchema
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
