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
