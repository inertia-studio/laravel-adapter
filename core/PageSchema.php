<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Filters\BaseFilter;
use JsonSerializable;

/**
 * Universal page composition primitive.
 *
 * Every page in the system (dashboard, CRUD, custom) is composed
 * from these primitives. Static factory methods return typed
 * schema objects that serialize to JSON for the frontend.
 */
class PageSchema implements JsonSerializable
{
    use HasSchema;

    protected string $type;

    protected string $label;

    /** @var array<string, mixed> */
    protected array $props = [];

    /** @var array<mixed> */
    protected array $children = [];

    protected function __construct(string $type, string $label = '')
    {
        $this->type = $type;
        $this->label = $label;
    }

    // ─── Layout Primitives ───────────────────────────────────

    /**
     * Grid layout with N columns.
     *
     * @param  array<PageSchema|Widget>  $children
     */
    public static function grid(int $columns, array $children): static
    {
        $schema = new static('grid');
        $schema->props['columns'] = $columns;
        $schema->children = $children;

        return $schema;
    }

    /**
     * Auto-sized row (columns = child count).
     *
     * @param  array<PageSchema|Widget>  $children
     */
    public static function row(array $children): static
    {
        return static::grid(count($children), $children);
    }

    /**
     * Tabbed sections.
     *
     * @param  array<Tab>  $tabs
     */
    public static function tabs(array $tabs): static
    {
        $schema = new static('tabs');
        $schema->children = $tabs;

        return $schema;
    }

    // ─── Content Primitives ──────────────────────────────────

    /**
     * Card with heading, description, and optional nested content.
     */
    public static function card(string $heading): static
    {
        return new static('card', $heading);
    }

    /**
     * Section — alias for card with different visual weight.
     */
    public static function section(string $heading): static
    {
        return new static('section', $heading);
    }

    /**
     * Raw HTML content.
     */
    public static function html(string $content): static
    {
        $schema = new static('html');
        $schema->props['content'] = $content;

        return $schema;
    }

    /**
     * Markdown content (rendered on frontend).
     */
    public static function markdown(string $content): static
    {
        $schema = new static('markdown');
        $schema->props['content'] = $content;

        return $schema;
    }

    /**
     * Asymmetric split layout (e.g. 2/3 + 1/3).
     *
     * @param  array<PageSchema|Widget>  $children
     */
    public static function split(int $left, int $right, array $children): static
    {
        $schema = new static('split');
        $schema->props['left'] = $left;
        $schema->props['right'] = $right;
        $schema->children = $children;

        return $schema;
    }

    /**
     * Vertical stack with consistent spacing.
     *
     * @param  array<PageSchema|Widget>  $children
     */
    public static function stack(array $children): static
    {
        $schema = new static('stack');
        $schema->children = $children;

        return $schema;
    }

    /**
     * Horizontal divider line.
     */
    public static function divider(): static
    {
        return new static('divider');
    }

    /**
     * Empty vertical spacer.
     */
    public static function spacer(): static
    {
        return new static('spacer');
    }

    /**
     * Alert banner (info, success, warning, danger).
     */
    public static function alert(string $message): static
    {
        $schema = new static('alert', $message);
        $schema->props['variant'] = 'info';

        return $schema;
    }

    /**
     * Inline badge pill.
     */
    public static function badge(string $text): static
    {
        $schema = new static('badge', $text);
        $schema->props['variant'] = 'info';

        return $schema;
    }

    /**
     * Progress bar (0-100).
     */
    public static function progress(int|float $value): static
    {
        $schema = new static('progress');
        $schema->props['value'] = $value;
        $schema->props['max'] = 100;

        return $schema;
    }

    /**
     * Large metric display.
     */
    public static function metric(string $label, mixed $value): static
    {
        $schema = new static('metric', $label);
        $schema->props['value'] = $value;

        return $schema;
    }

    /**
     * Empty state placeholder.
     */
    public static function empty(string $message = 'No data available.'): static
    {
        $schema = new static('empty');
        $schema->props['message'] = $message;

        return $schema;
    }

    /**
     * Image display.
     */
    public static function image(string $src): static
    {
        $schema = new static('image');
        $schema->props['src'] = $src;

        return $schema;
    }

    /**
     * Key-value list (definition list).
     *
     * @param  array<string, mixed>  $items
     */
    public static function list(array $items): static
    {
        $schema = new static('list');
        $schema->props['items'] = $items;

        return $schema;
    }

    /**
     * Activity timeline.
     *
     * @param  array<array{title: string, description?: string, time?: string, icon?: string, color?: string}>  $entries
     */
    public static function timeline(array $entries): static
    {
        $schema = new static('timeline');
        $schema->props['entries'] = $entries;

        return $schema;
    }

    /**
     * Button group.
     *
     * @param  array<PageActionBuilder>  $actions
     */
    public static function actions(array $actions): static
    {
        $schema = new static('actions');
        $schema->children = $actions;

        return $schema;
    }

    // ─── Data Primitives ─────────────────────────────────────

    /**
     * Inline data table on the page.
     * Reuses the same Column/Filter/Action builders as Module::table().
     */
    public static function table(string $label = ''): PageTableBuilder
    {
        return new PageTableBuilder($label);
    }

    /**
     * Inline form on the page.
     * Reuses the same Field/Layout builders as Module::form().
     */
    public static function form(string $label = ''): PageFormBuilder
    {
        return new PageFormBuilder($label);
    }

    /**
     * Detail/infolist view.
     */
    public static function detail(string $label = ''): PageDetailBuilder
    {
        return new PageDetailBuilder($label);
    }

    // ─── Interactive Primitives ──────────────────────────────

    /**
     * Page-level filters that control data on the page.
     *
     * @param  array<BaseFilter>  $filters
     */
    public static function filters(array $filters): static
    {
        $schema = new static('filters');
        $schema->children = $filters;

        return $schema;
    }

    /**
     * Action button (optionally with a modal form).
     */
    public static function action(string $label): PageActionBuilder
    {
        return new PageActionBuilder($label);
    }

    // ─── Builder Methods ─────────────────────────────────────

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description): static
    {
        $this->props['description'] = $description;

        return $this;
    }

    public function content(string $content): static
    {
        $this->props['content'] = $content;

        return $this;
    }

    /**
     * @param  array<PageSchema|Widget>  $children
     */
    public function schema(array $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->props['columns'] = $columns;

        return $this;
    }

    public function variant(string $variant): static
    {
        $this->props['variant'] = $variant;

        return $this;
    }

    public function color(string $color): static
    {
        $this->props['color'] = $color;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->props['icon'] = ['name' => $icon, 'provider' => null, 'variant' => null];

        return $this;
    }

    public function max(int|float $max): static
    {
        $this->props['max'] = $max;

        return $this;
    }

    public function size(string $size): static
    {
        $this->props['size'] = $size;

        return $this;
    }

    public function alt(string $alt): static
    {
        $this->props['alt'] = $alt;

        return $this;
    }

    public function width(string $width): static
    {
        $this->props['width'] = $width;

        return $this;
    }

    public function height(string $height): static
    {
        $this->props['height'] = $height;

        return $this;
    }

    public function rounded(bool $rounded = true): static
    {
        $this->props['rounded'] = $rounded;

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
            ...$this->props,
        ];

        if (! empty($this->children)) {
            $data['schema'] = array_map(
                fn (mixed $child) => $child instanceof JsonSerializable ? $child->toArray() : $child,
                $this->children,
            );
        }

        return $data;
    }
}

// ─── Sub-Builders ────────────────────────────────────────────

/**
 * Inline table builder for page schemas.
 */
class PageTableBuilder implements JsonSerializable
{
    use HasSchema;

    protected string $label;

    protected ?Closure $query = null;

    protected ?string $moduleClass = null;

    /** @var array<mixed> */
    protected array $columns = [];

    /** @var array<mixed> */
    protected array $filters = [];

    /** @var array<mixed> */
    protected array $actions = [];

    protected ?array $defaultSort = null;

    protected bool $searchable = true;

    protected bool $paginated = true;

    protected int $poll = 0;

    public function __construct(string $label = '')
    {
        $this->label = $label;
    }

    /**
     * Pull columns/filters/actions from an existing Module class.
     *
     * @param  class-string<Module>  $moduleClass
     */
    public function using(string $moduleClass): static
    {
        $this->moduleClass = $moduleClass;

        return $this;
    }

    public function query(Closure $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param  array<mixed>  $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param  array<mixed>  $filters
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param  array<mixed>  $actions
     */
    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSort = ['column' => $column, 'direction' => $direction];

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function paginated(bool $paginated = true): static
    {
        $this->paginated = $paginated;

        return $this;
    }

    public function poll(int $seconds = 5): static
    {
        $this->poll = $seconds;

        return $this;
    }

    public function getQuery(): ?Closure
    {
        return $this->query;
    }

    public function getModuleClass(): ?string
    {
        return $this->moduleClass;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        // If using a module, pull its table schema
        if ($this->moduleClass) {
            $table = ($this->moduleClass)::table(new Table);
            $tableSchema = $table->toSchema();
            $tableSchema['type'] = 'table';
            $tableSchema['label'] = $this->label;

            return $tableSchema;
        }

        return [
            'type' => 'table',
            'label' => $this->label,
            'columns' => array_map(fn ($c) => $c->toArray(), $this->columns),
            'filters' => array_map(fn ($f) => $f->toArray(), $this->filters),
            'actions' => array_map(fn ($a) => $a->toArray(), $this->actions),
            'bulkActions' => [],
            'searchable' => $this->searchable,
            'paginated' => $this->paginated,
            'defaultSort' => $this->defaultSort,
            'filterColumns' => 2,
            'poll' => $this->poll,
        ];
    }
}

/**
 * Inline form builder for page schemas.
 */
class PageFormBuilder implements JsonSerializable
{
    use HasSchema;

    protected string $label;

    /** @var array<mixed> */
    protected array $fields = [];

    protected string $operation = 'create';

    /** @var string|array<string>|Closure|null */
    protected string|array|Closure|null $action = null;

    protected string $method = 'POST';

    protected ?string $key = null;

    public function __construct(string $label = '')
    {
        $this->label = $label;
    }

    /**
     * @param  array<mixed>  $fields
     */
    public function schema(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set the form action handler.
     *
     * Accepts:
     *  - A Closure:  fn(Request $request) => redirect()->back()
     *  - A controller action: [SettingsController::class, 'save']
     *  - A raw URL string: '/admin/settings'
     *
     * @param  string|array<string>|Closure  $action
     */
    public function action(string|array|Closure $action, string $method = 'POST'): static
    {
        $this->action = $action;
        $this->method = $method;

        return $this;
    }

    /**
     * Set an explicit form key (used in route generation for closure actions).
     * Defaults to slugified label.
     */
    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function operation(string $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get the raw action handler.
     *
     * @return string|array<string>|Closure|null
     */
    public function getAction(): string|array|Closure|null
    {
        return $this->action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getKey(): string
    {
        if ($this->key) {
            return $this->key;
        }

        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->label ?: 'form'), '-'));
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Resolve the action URL for serialization.
     * The adapter layer should call this with the resolved URL.
     */
    public function resolvedActionUrl(?string $url): array
    {
        $form = new Form;
        $form->schema($this->fields);
        $formSchema = $form->toSchema($this->operation);

        return [
            'type' => 'form',
            'label' => $this->label,
            'actionUrl' => $url,
            'method' => $this->method,
            ...$formSchema,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $form = new Form;
        $form->schema($this->fields);
        $formSchema = $form->toSchema($this->operation);

        // Raw URL string — pass through directly
        $actionUrl = is_string($this->action) ? $this->action : null;

        return [
            'type' => 'form',
            'label' => $this->label,
            'actionUrl' => $actionUrl,
            'method' => $this->method,
            ...$formSchema,
        ];
    }
}

/**
 * Inline detail view builder for page schemas.
 */
class PageDetailBuilder implements JsonSerializable
{
    use HasSchema;

    protected string $label;

    /** @var array<mixed> */
    protected array $entries = [];

    public function __construct(string $label = '')
    {
        $this->label = $label;
    }

    /**
     * @param  array<mixed>  $entries
     */
    public function schema(array $entries): static
    {
        $this->entries = $entries;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'detail',
            'label' => $this->label,
            'schema' => array_map(fn ($e) => $e->toArray(), $this->entries),
        ];
    }
}

/**
 * Page action button builder.
 */
class PageActionBuilder implements JsonSerializable
{
    use HasSchema;

    protected string $label;

    protected ?string $iconName = null;

    /** @var string|array<string>|Closure|null */
    protected string|array|Closure|null $action = null;

    protected string $method = 'POST';

    protected string $color = 'primary';

    protected bool $requiresConfirmation = false;

    protected ?string $confirmationMessage = null;

    /** @var array<mixed>|null */
    protected ?array $modalFields = null;

    protected ?string $key = null;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function icon(string $name): static
    {
        $this->iconName = $name;

        return $this;
    }

    /**
     * Set the action handler.
     *
     * Accepts:
     *  - A Closure:  fn(Request $request) => redirect()->back()
     *  - A controller action: [Controller::class, 'method']
     *  - A raw URL string: '/admin/do-something'
     *
     * @param  string|array<string>|Closure  $action
     */
    public function action(string|array|Closure $action, string $method = 'POST'): static
    {
        $this->action = $action;
        $this->method = $method;

        return $this;
    }

    /**
     * @deprecated Use action() instead
     */
    public function url(string $url, string $method = 'POST'): static
    {
        return $this->action($url, $method);
    }

    /**
     * Set an explicit action key (used in route generation for closure actions).
     */
    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function requiresConfirmation(string $message = 'Are you sure?'): static
    {
        $this->requiresConfirmation = true;
        $this->confirmationMessage = $message;

        return $this;
    }

    /**
     * @param  array<mixed>  $fields
     */
    public function modal(array $fields): static
    {
        $this->modalFields = $fields;

        return $this;
    }

    /**
     * @return string|array<string>|Closure|null
     */
    public function getAction(): string|array|Closure|null
    {
        return $this->action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getKey(): string
    {
        if ($this->key) {
            return $this->key;
        }

        return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->label), '-'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => 'action',
            'label' => $this->label,
            'icon' => $this->iconName ? ['name' => $this->iconName, 'provider' => null, 'variant' => null] : null,
            'url' => is_string($this->action) ? $this->action : null,
            'method' => $this->method,
            'color' => $this->color,
            'requiresConfirmation' => $this->requiresConfirmation,
            'confirmationMessage' => $this->confirmationMessage,
        ];

        if ($this->modalFields !== null) {
            $form = new Form;
            $form->schema($this->modalFields);
            $data['modal'] = $form->toSchema();
        }

        return $data;
    }

    /**
     * Serialize with a resolved URL (for closure/array actions).
     *
     * @return array<string, mixed>
     */
    public function resolvedUrl(string $url): array
    {
        $data = $this->toArray();
        $data['url'] = $url;

        return $data;
    }
}
