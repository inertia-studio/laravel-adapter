<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class LayoutConfig implements JsonSerializable
{
    use HasSchema;

    protected string $maxWidth = '7xl';

    protected string $contentWidth = '100%';

    protected string $gutter = 'md';

    /** @var array<string, mixed> */
    protected array $sidebar = [
        'style' => 'dark',
        'width' => '280px',
        'collapsible' => true,
        'collapsedWidth' => '64px',
        'defaultCollapsed' => false,
        'mobileBreakpoint' => 'lg',
    ];

    /** @var array<string, mixed> */
    protected array $topbar = [
        'sticky' => true,
        'height' => '64px',
        'showBreadcrumbs' => true,
        'showSearch' => true,
        'showUserMenu' => true,
    ];

    /** @var array<string, mixed> */
    protected array $navigation = [
        'style' => 'sidebar',
        'groupStyle' => 'collapsible',
        'iconSize' => 'md',
        'activeStyle' => 'highlight',
    ];

    /** @var array<string, mixed> */
    protected array $footer = [
        'enabled' => false,
        'text' => null,
        'sticky' => false,
    ];

    private const MAX_WIDTH_MAP = [
        'full' => '100%',
        '7xl' => '80rem',
        '6xl' => '72rem',
        '5xl' => '64rem',
        '4xl' => '56rem',
        '3xl' => '48rem',
        '2xl' => '42rem',
    ];

    private const GUTTER_MAP = [
        'none' => '0',
        'sm' => '1rem',
        'md' => '1.5rem',
        'lg' => '2rem',
        'xl' => '2.5rem',
    ];

    public static function make(): static
    {
        return new static;
    }

    public function maxWidth(string $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function contentWidth(string $width): static
    {
        $this->contentWidth = $width;

        return $this;
    }

    public function gutter(string $gutter): static
    {
        $this->gutter = $gutter;

        return $this;
    }

    public function sidebar(
        ?string $style = null,
        ?string $width = null,
        ?bool $collapsible = null,
        ?string $collapsedWidth = null,
        ?bool $defaultCollapsed = null,
        ?string $mobileBreakpoint = null,
    ): static {
        if ($style !== null) {
            $this->sidebar['style'] = $style;
        }
        if ($width !== null) {
            $this->sidebar['width'] = $width;
        }
        if ($collapsible !== null) {
            $this->sidebar['collapsible'] = $collapsible;
        }
        if ($collapsedWidth !== null) {
            $this->sidebar['collapsedWidth'] = $collapsedWidth;
        }
        if ($defaultCollapsed !== null) {
            $this->sidebar['defaultCollapsed'] = $defaultCollapsed;
        }
        if ($mobileBreakpoint !== null) {
            $this->sidebar['mobileBreakpoint'] = $mobileBreakpoint;
        }

        return $this;
    }

    public function topbar(
        ?bool $sticky = null,
        ?string $height = null,
        ?bool $showBreadcrumbs = null,
        ?bool $showSearch = null,
        ?bool $showUserMenu = null,
    ): static {
        if ($sticky !== null) {
            $this->topbar['sticky'] = $sticky;
        }
        if ($height !== null) {
            $this->topbar['height'] = $height;
        }
        if ($showBreadcrumbs !== null) {
            $this->topbar['showBreadcrumbs'] = $showBreadcrumbs;
        }
        if ($showSearch !== null) {
            $this->topbar['showSearch'] = $showSearch;
        }
        if ($showUserMenu !== null) {
            $this->topbar['showUserMenu'] = $showUserMenu;
        }

        return $this;
    }

    public function navigation(
        ?string $style = null,
        ?string $groupStyle = null,
        ?string $iconSize = null,
        ?string $activeStyle = null,
    ): static {
        if ($style !== null) {
            $this->navigation['style'] = $style;
        }
        if ($groupStyle !== null) {
            $this->navigation['groupStyle'] = $groupStyle;
        }
        if ($iconSize !== null) {
            $this->navigation['iconSize'] = $iconSize;
        }
        if ($activeStyle !== null) {
            $this->navigation['activeStyle'] = $activeStyle;
        }

        return $this;
    }

    public function footer(
        ?bool $enabled = null,
        ?string $text = null,
        ?bool $sticky = null,
    ): static {
        if ($enabled !== null) {
            $this->footer['enabled'] = $enabled;
        }
        if ($text !== null) {
            $this->footer['text'] = $text;
        }
        if ($sticky !== null) {
            $this->footer['sticky'] = $sticky;
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'maxWidth' => self::MAX_WIDTH_MAP[$this->maxWidth] ?? $this->maxWidth,
            'contentWidth' => $this->contentWidth,
            'gutter' => self::GUTTER_MAP[$this->gutter] ?? $this->gutter,
            'sidebar' => $this->sidebar,
            'topbar' => $this->topbar,
            'navigation' => $this->navigation,
            'footer' => $this->footer,
        ];
    }
}
