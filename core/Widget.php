<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Widget implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected string $type;

    protected string $label;

    protected mixed $value = null;

    protected ?string $description = null;

    protected ?string $change = null;

    protected ?string $color = null;

    protected int $columnSpan = 1;

    /** @var array<mixed> */
    protected array $schema = [];

    /** @var array<string, mixed> */
    protected array $extra = [];

    protected function __construct(string $type, string $label)
    {
        $this->type = $type;
        $this->label = $label;
    }

    // ─── Stat Widgets ────────────────────────────────────────

    public static function stat(string $label): static
    {
        return new static('stat', $label);
    }

    /**
     * @param  array<Widget>  $stats
     */
    public static function statGroup(array $stats): static
    {
        $widget = new static('stat-group', '');
        $widget->schema = $stats;

        return $widget;
    }

    // ─── Chart Widgets ────────────────────────────────────────

    /**
     * Line chart.
     *
     * @param  array<array{label: string, value: int|float}>|Closure  $data
     */
    public static function line(string $label, array|Closure $data = []): static
    {
        $widget = new static('chart', $label);
        $widget->extra['chartType'] = 'line';
        $widget->extra['data'] = $data instanceof Closure ? ($data)() : $data;

        return $widget;
    }

    /**
     * Area chart (filled line).
     *
     * @param  array<array{label: string, value: int|float}>|Closure  $data
     */
    public static function area(string $label, array|Closure $data = []): static
    {
        $widget = new static('chart', $label);
        $widget->extra['chartType'] = 'area';
        $widget->extra['data'] = $data instanceof Closure ? ($data)() : $data;

        return $widget;
    }

    /**
     * Bar chart.
     *
     * @param  array<array{label: string, value: int|float}>|Closure  $data
     */
    public static function bar(string $label, array|Closure $data = []): static
    {
        $widget = new static('chart', $label);
        $widget->extra['chartType'] = 'bar';
        $widget->extra['data'] = $data instanceof Closure ? ($data)() : $data;

        return $widget;
    }

    /**
     * Donut/Pie chart.
     *
     * @param  array<array{label: string, value: int|float, color?: string}>|Closure  $data
     */
    public static function donut(string $label, array|Closure $data = []): static
    {
        $widget = new static('chart', $label);
        $widget->extra['chartType'] = 'donut';
        $widget->extra['data'] = $data instanceof Closure ? ($data)() : $data;

        return $widget;
    }

    /**
     * Sparkline — compact inline chart (no labels, no axes).
     *
     * @param  array<int|float>|Closure  $values
     */
    public static function sparkline(array|Closure $values = []): static
    {
        $widget = new static('sparkline', '');
        $resolvedValues = $values instanceof Closure ? ($values)() : $values;
        $widget->extra['data'] = array_map(
            fn (int|float $v, int $i) => ['label' => (string) $i, 'value' => $v],
            array_values($resolvedValues),
            array_keys($resolvedValues),
        );

        return $widget;
    }

    // ─── Activity / Timeline Widgets ─────────────────────────

    /**
     * Activity log timeline.
     *
     * @param  array<array{title: string, description?: string, time?: string, icon?: string, color?: string, user?: string}>|Closure  $entries
     */
    public static function activity(string $label, array|Closure $entries = []): static
    {
        $widget = new static('activity', $label);
        $widget->extra['entries'] = $entries instanceof Closure ? ($entries)() : $entries;

        return $widget;
    }

    // ─── Content Widgets ─────────────────────────────────────

    public static function section(string $heading): static
    {
        return new static('section', $heading);
    }

    public static function card(string $label): static
    {
        return new static('card', $label);
    }

    // ─── Grid Layout ─────────────────────────────────────────

    /**
     * @param  array<Widget>  $children
     */
    public static function grid(int $columns, array $children): static
    {
        $widget = new static('grid', '');
        $widget->extra['columns'] = $columns;
        $widget->schema = $children;

        return $widget;
    }

    /**
     * @param  array<Widget>  $children
     */
    public static function row(array $children): static
    {
        return static::grid(count($children), $children);
    }

    // ─── Builder Methods ─────────────────────────────────────

    public function value(mixed $value): static
    {
        $this->value = $value instanceof Closure ? ($value)() : $value;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function change(string $change): static
    {
        $this->change = $change;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function columns(int $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function height(string $height): static
    {
        $this->extra['height'] = $height;

        return $this;
    }

    /**
     * Chart colors for multi-series or donut segments.
     *
     * @param  array<string>  $colors
     */
    public function colors(array $colors): static
    {
        $this->extra['colors'] = $colors;

        return $this;
    }

    /**
     * Chart data (alternative to constructor param).
     *
     * @param  array<array{label: string, value: int|float}>|Closure  $data
     */
    public function data(array|Closure $data): static
    {
        $this->extra['data'] = $data instanceof Closure ? ($data)() : $data;

        return $this;
    }

    /**
     * Multi-series data for stacked/grouped charts.
     *
     * @param  array<array{name: string, data: array<int|float>, color?: string}>  $series
     */
    public function series(array $series): static
    {
        $this->extra['series'] = $series;

        return $this;
    }

    /**
     * X-axis labels for multi-series charts.
     *
     * @param  array<string>  $labels
     */
    public function labels(array $labels): static
    {
        $this->extra['labels'] = $labels;

        return $this;
    }

    /**
     * Stack bars/areas on top of each other (vs side-by-side).
     */
    public function stacked(bool $stacked = true): static
    {
        $this->extra['stacked'] = $stacked;

        return $this;
    }

    public function content(string $content): static
    {
        $this->extra['content'] = $content;

        return $this;
    }

    /**
     * @param  array<Widget>  $schema
     */
    public function schema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'label' => $this->label,
            'icon' => $this->getIconSchema(),
            'columnSpan' => $this->columnSpan,
        ];

        if ($this->value !== null) {
            $data['value'] = $this->value;
        }
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->change !== null) {
            $data['change'] = $this->change;
        }
        if ($this->color !== null) {
            $data['color'] = $this->color;
        }
        if (! empty($this->schema)) {
            $data['schema'] = array_map(
                fn (mixed $item) => $item instanceof JsonSerializable ? $item->toArray() : $item,
                $this->schema,
            );
        }

        return array_merge($data, $this->extra);
    }
}
