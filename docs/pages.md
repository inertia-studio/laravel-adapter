---
title: Pages — Inertia Studio
---

# Pages

Inertia Studio supports custom dashboard pages — full-screen views with widgets, metrics, forms, and tables — rendered at `/admin/pages/{slug}`.

## Base class: `DashboardPage`

All custom pages extend `InertiaStudio\Pages\DashboardPage`:

```php
use InertiaStudio\Pages\DashboardPage;

class Analytics extends DashboardPage
{
    protected string $title = 'Analytics';

    public function schema(): array
    {
        return [
            // widgets, metrics, forms, tables…
        ];
    }
}
```

### Required method

| Method | Description |
|--------|-------------|
| `schema(): array` | Return an array of `PageSchema`, `Widget`, `PageTableBuilder`, `PageFormBuilder`, or `PageActionBuilder` instances. |

### Optional properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$title` | `string` | `'Dashboard'` | Page heading and default sidebar label. |
| `$description` | `string\|null` | `null` | Subtitle shown below the heading. |
| `$navigationPosition` | `string` | `'after-list'` | Sidebar placement: `'before-list'` or `'after-list'`. |
| `$hiddenFromNavigation` | `bool` | `false` | Set to `true` to exclude from the sidebar. |

### Access control

Override `canAccess(mixed $user): bool` to restrict access:

```php
public function canAccess(mixed $user): bool
{
    return $user->hasRole('admin');
}
```

### Icon in the sidebar

Use the `icon()` method (provided by `HasIcon`) or set properties directly:

```php
class Analytics extends DashboardPage
{
    protected string $title = 'Analytics';

    public function __construct()
    {
        $this->icon('chart-bar');
    }

    public function schema(): array { /* … */ }
}
```

## Generating a page

```bash
php artisan studio:page Analytics --panel=Admin
```

Creates `app/Studio/Admin/Pages/Analytics.php` with the correct namespace, title, and empty `schema()`.

## File location and routing

Pages must live at `app/Studio/{Panel}/Pages/{Name}.php`. They are auto-discovered by the panel — no registration step is needed.

| File | URL | Sidebar label |
|------|-----|---------------|
| `Pages/Analytics.php` | `/admin/pages/analytics` | `Analytics` (from `$title`) |
| `Pages/RevenueReport.php` | `/admin/pages/revenue-report` | `Revenue Report` |

The special file `Pages/Dashboard.php` is used as the panel's main dashboard view (served at `/admin`) and is **not** surfaced in the sidebar as a regular page.

## Sidebar navigation

Custom pages are auto-discovered and added to the sidebar automatically. No manual registration in `navigationGroups()` is needed.

Use `$navigationPosition` to control placement relative to modules:

```php
// Appears above all modules in the sidebar
protected string $navigationPosition = 'before-list';

// Appears below all modules in the sidebar (default)
protected string $navigationPosition = 'after-list';
```

To hide a page from the sidebar while keeping it routable:

```php
protected bool $hiddenFromNavigation = true;
```

## `DashboardPage` vs `Page`

`InertiaStudio\Page` is the legacy builder-style class (deprecated). Use `DashboardPage` for all new pages — it provides the `schema()` contract, navigation metadata, and access control hook that the framework expects.

## Example: Analytics page with a metric grid

```php
use InertiaStudio\Pages\DashboardPage;
use InertiaStudio\PageSchema;
use InertiaStudio\Widget;

class Analytics extends DashboardPage
{
    protected string $title = 'Analytics';

    public function schema(): array
    {
        return [
            PageSchema::grid([
                Widget::metric('Total Users', fn () => User::count()),
                Widget::metric('Revenue', fn () => Order::sum('total')),
                Widget::metric('Signups Today', fn () => User::whereDate('created_at', today())->count()),
            ]),
        ];
    }
}
```

See [Page Schema](page-schema.md) for the full list of schema building blocks.
