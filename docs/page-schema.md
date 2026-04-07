---
title: Page Schema — Inertia Studio
---

# Page Schema

`PageSchema` is the universal composition primitive for every custom page. Return an array of `PageSchema` items from `DashboardPage::schema()` to build the page layout.

## Layout primitives

### `PageSchema::grid(int $columns, array $children)`

A responsive column grid.

```php
PageSchema::grid(3, [
    Widget::stat('Users')->value(User::count()),
    Widget::stat('Revenue')->value('$12,400'),
    Widget::stat('Orders')->value(Order::count()),
])
```

### `PageSchema::row(array $children)`

Auto-sized row — column count equals child count.

```php
PageSchema::row([
    Widget::stat('Active'),
    Widget::stat('Pending'),
])
```

### `PageSchema::split(int $left, int $right, array $children)`

Asymmetric split layout (ratios, e.g. `split(2, 1, [...])`).

```php
PageSchema::split(2, 1, [
    Widget::area('Revenue', fn () => $data),
    Widget::stat('Total')->value($total),
])
```

### `PageSchema::stack(array $children)`

Vertical stack with consistent spacing.

```php
PageSchema::stack([
    PageSchema::section('Settings'),
    PageSchema::form()->schema([...]),
])
```

### `PageSchema::tabs(array $tabs)`

Tabbed sections. Each tab is a `Tab` instance.

```php
use InertiaStudio\Tab;

PageSchema::tabs([
    Tab::make('Overview')->schema([...]),
    Tab::make('Details')->schema([...]),
])
```

---

## Content primitives

| Method | Description |
|--------|-------------|
| `PageSchema::card(string $heading)` | Card with a heading and optional `->schema([...])` children. |
| `PageSchema::section(string $heading)` | Section heading with lighter visual weight than a card. |
| `PageSchema::html(string $content)` | Raw HTML block. |
| `PageSchema::markdown(string $content)` | Markdown content rendered on the frontend. |
| `PageSchema::metric(string $label, mixed $value)` | Large stat display. Value may be a scalar or closure. |
| `PageSchema::progress(int\|float $value)` | Progress bar 0–100. Chain `->max($n)` to change the maximum. |
| `PageSchema::alert(string $message)` | Alert banner. Chain `->variant('success'\|'warning'\|'danger'\|'info')`. |
| `PageSchema::badge(string $text)` | Inline badge pill. Chain `->variant(...)` and `->color(...)`. |
| `PageSchema::list(array $items)` | Key-value definition list: `['Name' => $user->name, ...]`. |
| `PageSchema::timeline(array $entries)` | Activity timeline (see structure below). |
| `PageSchema::image(string $src)` | Image display. Chain `->width()`, `->height()`, `->alt()`, `->rounded()`. |
| `PageSchema::divider()` | Horizontal rule. |
| `PageSchema::spacer()` | Empty vertical gap. |
| `PageSchema::empty(string $message)` | Empty state placeholder. |

### Timeline entry shape

```php
PageSchema::timeline([
    ['title' => 'User registered', 'description' => 'via Google', 'time' => '2 min ago', 'icon' => 'user', 'color' => 'blue'],
])
```

---

## Data primitives

### `PageSchema::table(string $label = ''): PageTableBuilder`

Inline data table. Reuses the same `Column` / `Filter` / `Action` builders as `Module::table()`.

```php
PageSchema::table('Recent Orders')
    ->query(fn () => Order::latest()->limit(20)->get())
    ->columns([
        Column::text('id', '#'),
        Column::text('customer.name', 'Customer'),
        Column::money('total', 'Total'),
        Column::date('created_at', 'Date'),
    ])
    ->searchable(false)
    ->paginated(false)
```

Or pull columns/filters/actions from an existing module:

```php
PageSchema::table()->using(OrdersModule::class)
    ->query(fn () => Order::where('status', 'pending'))
```

### `PageSchema::form(string $label = ''): PageFormBuilder`

Inline form. The form action can be a closure, a controller action, or a URL.

```php
PageSchema::form('Site Settings')
    ->schema([
        Field::text('site_name', 'Site Name'),
        Field::text('support_email', 'Support Email'),
    ])
    ->action(fn (Request $request) => Settings::set($request->validated()))
```

### `PageSchema::action(string $label): PageActionBuilder`

Standalone action button.

```php
PageSchema::action('Export CSV')
    ->icon('arrow-down-tray')
    ->color('secondary')
    ->action(fn () => response()->streamDownload(...))
```

Chain `->requiresConfirmation('Are you sure?')` for a confirmation dialog, or `->modal([...fields])` for a modal form before the action fires.

---

## Widget reference

`Widget` instances can be used anywhere a `PageSchema` child is accepted, and also directly in `DashboardPage::schema()`.

| Factory | Description |
|---------|-------------|
| `Widget::stat(string $label)` | Single stat tile. Chain `->value(...)`, `->description(...)`, `->change('+12%')`, `->color(...)`, `->icon(...)`. |
| `Widget::statGroup(array $stats)` | Group of stat tiles without individual borders. |
| `Widget::line(string $label, $data)` | Line chart. `$data` is `[['label' => '...', 'value' => n], ...]` or a Closure. |
| `Widget::area(string $label, $data)` | Filled area chart. |
| `Widget::bar(string $label, $data)` | Bar chart. |
| `Widget::donut(string $label, $data)` | Donut / pie chart. Data entries may include `'color'`. |
| `Widget::sparkline($values)` | Compact inline chart. `$values` is a flat array of numbers. |
| `Widget::activity(string $label, $entries)` | Activity log timeline (same entry shape as `PageSchema::timeline`). |
| `Widget::grid(int $columns, array $children)` | Grid of child widgets. |
| `Widget::row(array $children)` | Auto-column grid (row). |
| `Widget::card(string $label)` | Card container. Chain `->schema([...widgets])`. |
| `Widget::section(string $heading)` | Section container. Chain `->schema([...widgets])`. |

### Common Widget builder methods

```php
Widget::stat('Revenue')
    ->value(fn () => Order::sum('total'))   // Closure resolved at render time
    ->description('All time')
    ->change('+8% this month')
    ->color('green')                         // green, red, blue, yellow, …
    ->icon('currency-dollar')
    ->columns(2)                             // span 2 grid columns
```

### Multi-series charts

```php
Widget::bar('Monthly Revenue')
    ->series([
        ['name' => '2023', 'data' => [1200, 900, 1500, ...]],
        ['name' => '2024', 'data' => [1800, 1200, 2100, ...]],
    ])
    ->labels(['Jan', 'Feb', 'Mar', ...])
    ->stacked()
    ->height('300px')
```
