<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Theme implements JsonSerializable
{
    use HasSchema;

    protected string $primary = '#2563eb';

    protected string $danger = '#dc2626';

    protected string $warning = '#d97706';

    protected string $success = '#16a34a';

    protected string $info = '#3b82f6';

    protected string $gray = '#64748b';

    protected string $fontFamily = 'Inter, system-ui, sans-serif';

    protected string $fontSize = '0.875rem';

    protected string $borderRadius = 'lg';

    protected string $density = 'comfortable';

    /** @var array<string, string> */
    protected array $lightColors = [];

    /** @var array<string, string> */
    protected array $darkColors = [];

    public static function make(): static
    {
        return new static;
    }

    public function primary(string $color): static
    {
        $this->primary = $color;

        return $this;
    }

    public function danger(string $color): static
    {
        $this->danger = $color;

        return $this;
    }

    public function warning(string $color): static
    {
        $this->warning = $color;

        return $this;
    }

    public function success(string $color): static
    {
        $this->success = $color;

        return $this;
    }

    public function info(string $color): static
    {
        $this->info = $color;

        return $this;
    }

    public function gray(string $color): static
    {
        $this->gray = $color;

        return $this;
    }

    public function fontFamily(string $font): static
    {
        $this->fontFamily = $font;

        return $this;
    }

    public function fontSize(string $size): static
    {
        $this->fontSize = $size;

        return $this;
    }

    public function borderRadius(string $radius): static
    {
        $this->borderRadius = $radius;

        return $this;
    }

    public function density(string $density): static
    {
        $this->density = $density;

        return $this;
    }

    /**
     * Override light mode surface colors.
     *
     * Keys: bg, surface, surfaceAlt, border, borderStrong, text,
     *       textSecondary, textMuted, textFaint, input, hover
     *
     * Values: RGB triplets like '249 250 251' or hex like '#f9fafb'
     *
     * @param  array<string, string>  $colors
     */
    public function lightColors(array $colors): static
    {
        $this->lightColors = $colors;

        return $this;
    }

    /**
     * Override dark mode surface colors.
     *
     * @param  array<string, string>  $colors
     */
    public function darkColors(array $colors): static
    {
        $this->darkColors = $colors;

        return $this;
    }

    /**
     * Apply a named preset palette.
     *
     * Presets: indigo, ocean, emerald, rose, amber, violet, slate, teal, crimson, midnight
     */
    public function preset(string $name): static
    {
        $presets = self::presets();

        if (! isset($presets[$name])) {
            return $this;
        }

        $preset = $presets[$name];
        $this->lightColors = array_merge($this->lightColors, ['accent' => $preset['light']]);
        $this->darkColors = array_merge($this->darkColors, ['accent' => $preset['dark']]);

        return $this;
    }

    /**
     * @return array<string, array{light: string, dark: string, label: string}>
     */
    public static function presets(): array
    {
        return [
            'indigo' => ['light' => '79 70 229', 'dark' => '124 105 255', 'label' => 'Indigo'],
            'ocean' => ['light' => '37 99 235', 'dark' => '96 165 250', 'label' => 'Ocean'],
            'emerald' => ['light' => '5 150 105', 'dark' => '52 211 153', 'label' => 'Emerald'],
            'rose' => ['light' => '225 29 72', 'dark' => '251 113 133', 'label' => 'Rose'],
            'amber' => ['light' => '217 119 6', 'dark' => '251 191 36', 'label' => 'Amber'],
            'violet' => ['light' => '124 58 237', 'dark' => '167 139 250', 'label' => 'Violet'],
            'slate' => ['light' => '51 65 85', 'dark' => '203 213 225', 'label' => 'Slate'],
            'teal' => ['light' => '13 148 136', 'dark' => '94 234 212', 'label' => 'Teal'],
            'crimson' => ['light' => '185 28 28', 'dark' => '248 113 113', 'label' => 'Crimson'],
            'midnight' => ['light' => '30 58 138', 'dark' => '147 197 253', 'label' => 'Midnight'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'primary' => $this->primary,
            'danger' => $this->danger,
            'warning' => $this->warning,
            'success' => $this->success,
            'info' => $this->info,
            'gray' => $this->gray,
            'fontFamily' => $this->fontFamily,
            'fontSize' => $this->fontSize,
            'borderRadius' => $this->borderRadius,
            'density' => $this->density,
            'lightColors' => $this->lightColors,
            'darkColors' => $this->darkColors,
        ];
    }
}
