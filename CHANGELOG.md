# Changelog

All notable changes to `inertia-studio/laravel-adapter` will be documented in this file.

## [0.1.0] — 2026-04-05

Initial release.

### Added

- **Panel system** — Multi-panel support with separate URL prefixes, auth guards, middleware, and branding
- **Module system** — CRUD from a single PHP class with auto-discovery from `app/Studio/{Panel}/Modules/`
- **30 field types** — text, email, password, url, tel, number, stepper, textarea, slug, money, percent, select, toggle, checkbox, checkboxList, radio, toggleButtons, date, time, dateRange, colorPicker, fileUpload, imageUpload, tags, keyValue, repeater, hidden, placeholder, belongsTo, richEditor
- **8 column types** — text, badge, boolean, image, icon, color, date, money
- **5 filter types** — select, ternary, date, boolean, query (server-side searchable)
- **6 detail entry types** — text, badge, boolean, image, date, money
- **6 action types** — view, edit, delete, bulkDelete, export, custom with confirmation modals
- **2 layout types** — section (collapsible, aside, columns), tabs
- **24 PageSchema primitives** — grid, row, split, stack, tabs, card, section, html, markdown, divider, spacer, alert, badge, progress, metric, empty, image, list, timeline, actions, table, form, detail, filters, action
- **Closure-based handlers** — Form submissions and action buttons on custom pages accept closures or controller references
- **Widget system** — stat, statGroup, section, card, grid, row
- **Theming** — 10 color presets, dark mode, semantic tokens, customizable font/radius/density
- **Navigation** — Groups with icons, collapsible, badges with colors, custom page links
- **Dashboard pages** — Custom dashboard via `Pages/Dashboard.php`, auto-discovered
- **Custom pages** — `studio:page` generator, DashboardPage base class, PageSchema composition
- **Simple modules** — Modal-based CRUD with `$simple = true`
- **Relations** — Relation managers with form/table on edit pages
- **Tabs** — List page tabs with per-tab queries and badges
- **Form reactivity** — `reactive()`, `afterStateUpdated()`, dependent fields via partial reloads
- **Form validation** — Auto-generated from field schema, `required()`, `unique()`, `rules()`
- **Soft deletes** — Auto-detected, trashed filter, restore/force-delete actions
- **Authorization** — Laravel Policy integration, auto-hiding of unauthorized UI elements
- **Global search** — Modules opt-in via `globalSearch()`, results grouped by module
- **File uploads** — Upload controller with disk/directory config, image support
- **Authentication** — Login page, guard-based auth, panel-scoped middleware
- **7 Artisan commands** — `studio:install`, `studio:module`, `studio:panel`, `studio:relation`, `studio:page`, `studio:widget`, `studio:publish`
- **Component publishing** — Three-layer override: package default → theme → published copy
- **InlineTable** — Self-contained table with client-side search/sort/pagination
- **InlineForm** — Standalone form component for custom pages
