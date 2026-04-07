<?php

namespace InertiaStudio\Pages;

use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

abstract class DashboardPage implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected string $title = 'Dashboard';

    protected ?string $description = null;

    /** Controls sidebar placement: 'before-list' or 'after-list' (default). */
    protected string $navigationPosition = 'after-list';

    /** Set to true to hide this page from the sidebar entirely. */
    protected bool $hiddenFromNavigation = false;

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
     * URL slug derived from the class name (kebab-case, "Page" suffix stripped).
     */
    public function getSlug(): string
    {
        return str(class_basename(static::class))->kebab()->toString();
    }

    /**
     * Label shown in the sidebar. Defaults to the page title.
     */
    public function getNavigationLabel(): string
    {
        return $this->title;
    }

    public function getNavigationPosition(): string
    {
        return $this->navigationPosition;
    }

    public function isHiddenFromNavigation(): bool
    {
        return $this->hiddenFromNavigation;
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
