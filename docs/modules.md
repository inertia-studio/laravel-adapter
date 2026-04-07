---
title: Modules — Inertia Studio
---

# Modules

A module is a CRUD resource that drives a table list, create/edit form, and optional detail view. Each module maps to an Eloquent model.

## Generating a module

```bash
php artisan studio:module Users --panel=Admin
```

Creates `app/Studio/Admin/Modules/Users.php`.

## Anatomy of a module

```php
use App\Models\User;
use InertiaStudio\Column;
use InertiaStudio\Field;
use InertiaStudio\Filter;
use InertiaStudio\Module;
use InertiaStudio\Table;
use InertiaStudio\Form;

class Users extends Module
{
    protected static string $model = User::class;
    protected static string $navigationIcon = 'users';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Column::text('name', 'Name')->searchable()->sortable(),
                Column::text('email', 'Email')->searchable(),
                Column::date('created_at', 'Joined'),
            ])
            ->filters([
                Filter::boolean('active', 'Active only'),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Field::text('name', 'Name')->required(),
            Field::email('email', 'Email')->required(),
            Field::password('password', 'Password'),
        ]);
    }
}
```

## Static properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$model` | `string` | `''` | Eloquent model class. **Required.** |
| `$navigationIcon` | `string` | `'rectangle-stack'` | Sidebar icon name. |
| `$navigationLabel` | `string\|null` | `null` | Custom sidebar label (defaults to class basename). |
| `$navigationGroup` | `string\|null` | `null` | Group the module under a named sidebar section. |
| `$navigationSort` | `int` | `0` | Sort order within the sidebar. |
| `$recordTitleAttribute` | `string` | `'id'` | Model attribute shown as the record title. |
| `$simple` | `bool` | `false` | Use the simple (modal-based) CRUD instead of dedicated pages. |

---

## Columns

Define which columns appear in the list table.

```php
Column::text('email', 'Email')->searchable()->sortable()
Column::badge('status', 'Status')->colors(['active' => 'green', 'inactive' => 'red'])
Column::boolean('is_admin', 'Admin')
Column::date('created_at', 'Created')
Column::money('total', 'Amount')
Column::image('avatar', 'Avatar')
Column::icon('type', 'Type')
Column::color('brand_color', 'Brand')
```

### Common column methods

| Method | Description |
|--------|-------------|
| `->searchable()` | Include in global search. |
| `->sortable()` | Enable column-header sorting. |
| `->hidden()` | Hidden by default (user can toggle). |
| `->toggleable()` | User can show/hide the column. |
| `->label(string $label)` | Override the column heading. |

---

## Fields

Define the create/edit form schema.

```php
Field::text('name', 'Name')->required()
Field::email('email', 'Email')
Field::password('password', 'Password')
Field::textarea('bio', 'Bio')
Field::number('age', 'Age')->min(0)->max(120)
Field::money('price', 'Price')
Field::percent('discount', 'Discount')
Field::select('role', 'Role')->options(['admin' => 'Admin', 'user' => 'User'])
Field::checkbox('is_active', 'Active')
Field::toggle('featured', 'Featured')
Field::date('published_at', 'Publish Date')
Field::time('starts_at', 'Start Time')
Field::dateRange('period', 'Period')
Field::color('brand_color', 'Brand Color')
Field::tags('skills', 'Skills')
Field::slug('slug', 'Slug')->syncFrom('name')
Field::url('website', 'Website')
Field::tel('phone', 'Phone')
Field::masked('ssn', 'SSN')->mask('999-99-9999')
Field::rating('stars', 'Rating')
Field::otp('code', 'OTP Code')
Field::stepper('quantity', 'Qty')
Field::radio('plan', 'Plan')->options([...])
Field::checkboxList('permissions', 'Permissions')->options([...])
Field::toggleButtons('size', 'Size')->options([...])
Field::code('snippet', 'Code')->language('php')
Field::richEditor('body', 'Body')
Field::markdownEditor('notes', 'Notes')
Field::keyValue('meta', 'Meta')
Field::fileUpload('attachment', 'Attachment')
Field::imageUpload('avatar', 'Avatar')
Field::hidden('user_id', 'User')->default(fn () => auth()->id())
Field::placeholder('section_heading', 'Contact Info')
Field::repeater('addresses', 'Addresses')->schema([...])
Field::belongsTo('category_id', 'Category')
    ->relationship('category', 'name')
```

### Common field methods

| Method | Description |
|--------|-------------|
| `->required()` | Mark field as required. |
| `->nullable()` | Allow null. |
| `->disabled()` | Render as read-only. |
| `->default(mixed $value)` | Set a default value (scalar or closure). |
| `->hint(string $hint)` | Helper text shown below the field. |
| `->placeholder(string $text)` | Input placeholder. |
| `->columnSpan(int $n)` | How many form grid columns this field occupies. |
| `->rules(array $rules)` | Additional validation rules. |
| `->label(string $label)` | Override the field label. |
| `->createOnly()` | Show only on create form. |
| `->editOnly()` | Show only on edit form. |

---

## Filters

```php
Filter::boolean('active', 'Active')
Filter::select('status', 'Status')->options(['published' => 'Published', 'draft' => 'Draft'])
Filter::ternary('verified', 'Email Verified')
Filter::date('created_at', 'Created Date')
Filter::query('recent', 'Recent', fn ($q) => $q->where('created_at', '>=', now()->subDays(7)))
```

---

## Actions

Row actions (appear in the table row menu) and bulk actions are defined on the `Table`:

```php
use InertiaStudio\Action;

public static function table(Table $table): Table
{
    return $table
        ->columns([...])
        ->actions([
            Action::edit(),
            Action::delete(),
            Action::make('Ban', fn ($record) => $record->ban())
                ->color('danger')
                ->requiresConfirmation('Ban this user?'),
        ])
        ->bulkActions([
            Action::delete(),
        ]);
}
```

---

## Relations

```bash
php artisan studio:relation Posts --module=Users --panel=Admin
```

Attach a related module as a tab on the detail page:

```php
public static function relations(): array
{
    return [PostsRelation::class];
}
```

---

## Sidebar navigation badge

Show a count badge on the sidebar item:

```php
public static function navigationBadge(): string|int|null
{
    return User::where('status', 'pending')->count() ?: null;
}

public static function navigationBadgeColor(): string
{
    return 'warning'; // info, success, warning, danger
}
```
