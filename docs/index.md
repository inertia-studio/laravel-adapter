---
title: Inertia Studio — Documentation
---

# Inertia Studio

Server-driven admin panels for Laravel + Inertia.js. Define forms, tables, and pages in PHP — render with React, Vue, or Svelte.

## Quick links

- [Pages](pages.md) — Custom dashboard pages, the `schema()` contract, and sidebar navigation
- [Page Schema](page-schema.md) — Full reference for `PageSchema`, `Widget`, `Tab`, and layout helpers
- [Modules](modules.md) — CRUD modules, fields, columns, filters, and actions

## Installation

```bash
composer require inertia-studio/laravel-adapter
php artisan studio:install
```

## Generating a panel

```bash
php artisan studio:panel Admin
```

This creates `app/Studio/Admin/Admin.php`. Register it (auto-discovery handles this for most apps).

## Generating a module

```bash
php artisan studio:module Users --panel=Admin
```

Creates `app/Studio/Admin/Modules/Users.php` with a skeleton `table()`, `form()`, and `columns()` definition.

## Generating a custom page

```bash
php artisan studio:page Analytics --panel=Admin
```

Creates `app/Studio/Admin/Pages/Analytics.php` extending `DashboardPage` with an empty `schema()` method. See [Pages](pages.md) for details.
