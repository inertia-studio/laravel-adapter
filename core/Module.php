<?php

namespace InertiaStudio;

abstract class Module
{
    protected static string $model = '';

    protected static string $navigationIcon = 'rectangle-stack';

    protected static ?string $navigationLabel = null;

    protected static ?string $navigationGroup = null;

    protected static int $navigationSort = 0;

    protected static string $recordTitleAttribute = 'id';

    protected static bool $simple = false;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    /**
     * @return array<mixed>
     */
    public static function detail(): array
    {
        return [];
    }

    /**
     * @return array<class-string<Relation>>
     */
    public static function relations(): array
    {
        return [];
    }

    /**
     * @return array<Tab>
     */
    public static function tabs(): array
    {
        return [];
    }

    /**
     * @return array<Page>
     */
    public static function pages(): array
    {
        return [];
    }

    /**
     * @return array<Widget>
     */
    public static function widgets(): array
    {
        return [];
    }

    /**
     * Badge count shown in sidebar navigation.
     * Return null to hide badge.
     */
    public static function navigationBadge(): string|int|null
    {
        return null;
    }

    /**
     * Badge color: 'info' (default), 'success', 'warning', 'danger'
     */
    public static function navigationBadgeColor(): string
    {
        return 'info';
    }

    /**
     * Columns searchable via global search.
     * Return empty array to exclude from search.
     *
     * @return array<string>
     */
    public static function globalSearch(): array
    {
        return [];
    }

    public static function getSlug(): string
    {
        return str(class_basename(static::class))
            ->kebab()
            ->toString();
    }

    public static function getLabel(): string
    {
        return str(class_basename(static::class))
            ->snake()
            ->replace('_', ' ')
            ->singular()
            ->title()
            ->toString();
    }

    public static function getPluralLabel(): string
    {
        return str(class_basename(static::class))
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getPluralLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationSort(): int
    {
        return static::$navigationSort;
    }

    public static function getNavigationIcon(): string
    {
        return static::$navigationIcon;
    }

    public static function getModel(): string
    {
        return static::$model;
    }

    public static function getRecordTitleAttribute(): string
    {
        return static::$recordTitleAttribute;
    }

    public static function isSimple(): bool
    {
        return static::$simple;
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'slug' => static::getSlug(),
            'label' => static::getLabel(),
            'pluralLabel' => static::getPluralLabel(),
            'icon' => [
                'name' => static::$navigationIcon,
                'provider' => null,
                'variant' => null,
            ],
            'simple' => static::$simple,
            'recordTitleAttribute' => static::$recordTitleAttribute,
        ];
    }
}
