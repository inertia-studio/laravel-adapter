<?php

namespace InertiaStudio\Pages;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

abstract class DashboardPage implements JsonSerializable
{
    use HasSchema;

    protected string $title = 'Dashboard';

    protected ?string $description = null;

    /**
     * @return array<mixed>
     */
    abstract public function schema(): array;

    /**
     * Determine if the current user can access this page.
     * Override to restrict access.
     */
    public function canAccess(mixed $user): bool
    {
        return true;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'schema' => array_map(
                fn (mixed $item) => is_array($item) ? $item : $item->toArray(),
                $this->schema(),
            ),
        ];
    }
}
