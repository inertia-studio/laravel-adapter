# Inertia Studio — Laravel Adapter

Server-driven admin panels for Laravel + Inertia.js. Define forms, tables, and pages in PHP — render with React, Vue, or Svelte.

## Installation

```bash
composer require inertia-studio/laravel-adapter
npm install @inertia-studio/ui
php artisan studio:install
```

## Quick Example

```php
class Users extends Module
{
    protected static string $model = User::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Layout::section('User Information')->columns(2)->schema([
                Field::text('name')->required(),
                Field::email('email')->required()->unique(),
                Field::select('role')->options([
                    'admin' => 'Admin',
                    'editor' => 'Editor',
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Column::text('name')->searchable()->sortable(),
            Column::badge('role')->colors(['admin' => 'danger']),
            Column::date('created_at')->label('Joined'),
        ]);
    }
}
```

## What's Included

**PHP Builders** — 30 field types, 8 column types, 5 filter types, 6 detail types, 24 page primitives

**Laravel Integration** — Auto-discovery, Eloquent query building, policy authorization, file uploads, global search, 7 Artisan commands

**Panel System** — Multi-panel support, 10 theme presets, dark mode, navigation groups with badges

**Page Builder** — Compose dashboards, settings pages, and analytics from PHP using `PageSchema` primitives with closure-based form/action handlers

## Requirements

- PHP 8.4+
- Laravel 12 or 13
- Inertia.js v3

## Documentation

[inertia-studio.github.io](https://inertia-studio.github.io)

## License

MIT
