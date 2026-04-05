<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use InertiaStudio\Filters\QueryFilter;
use InertiaStudio\Form;
use InertiaStudio\Laravel\Concerns\HasAuthorization;
use InertiaStudio\Laravel\PanelManager;
use InertiaStudio\Laravel\Table\QueryBuilder;
use InertiaStudio\Table;

class ModuleController extends Controller
{
    use HasAuthorization;

    public function index(Request $request, string $module): Response
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        abort_unless($this->canViewAny($moduleClass), 403);

        $table = $moduleClass::table(new Table);
        $query = $moduleClass::getModel()::query();

        // Eager load relationships referenced in columns (dot notation)
        $eagerLoads = collect($table->getColumns())
            ->map(fn ($col) => $col->getName())
            ->filter(fn ($name) => str_contains($name, '.'))
            ->map(fn ($name) => explode('.', $name)[0])
            ->unique()
            ->values()
            ->toArray();

        if (! empty($eagerLoads)) {
            $query->with($eagerLoads);
        }

        $query = QueryBuilder::apply($query, $table, [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'filters' => $request->input('filter', []),
        ]);

        $perPage = $request->integer('perPage', 15);
        $records = $query->paginate($perPage)->withQueryString();

        $tableSchema = $table->toSchema();
        $tableSchema['actions'] = $this->applyActionAuthorization($tableSchema['actions'], $moduleClass);

        return Inertia::render('Studio::ListRecords', [
            'panel' => $manager->schema(),
            'module' => $moduleClass::meta(),
            'tableSchema' => $tableSchema,
            'records' => $records,
            'tabs' => $moduleClass::tabs(),
        ]);
    }

    public function filterOptions(Request $request, string $module, string $filter): JsonResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $table = $moduleClass::table(new Table);
        $filters = $table->getFilters();

        // Find the filter by name
        $filterDef = null;
        foreach ($filters as $f) {
            if ($f->getName() === $filter) {
                $filterDef = $f;
                break;
            }
        }

        if (! $filterDef || ! ($filterDef instanceof QueryFilter)) {
            return response()->json([]);
        }

        $relationName = $filterDef->getRelationshipName();
        $titleColumn = $filterDef->getRelationshipTitleColumn();

        if (! $relationName || ! $titleColumn) {
            return response()->json([]);
        }

        // Get the related model via the module's model relationship
        $model = new ($moduleClass::getModel());
        $relation = $model->{$relationName}();
        $relatedModel = $relation->getRelated();

        $query = $relatedModel::query();

        // Apply search
        $search = $request->input('search', '');
        if ($search) {
            $query->where($titleColumn, 'LIKE', "%{$search}%");
        }

        $results = $query->limit(50)->get()->map(fn ($record) => [
            'value' => (string) $record->getKey(),
            'label' => $record->{$titleColumn},
        ]);

        return response()->json($results);
    }

    public function relationOptions(Request $request, string $module, string $field): JsonResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        // Find the belongsTo field in the form schema
        $form = $moduleClass::form(new Form);
        $fieldDef = $this->findBelongsToField($form->toSchema()['schema'], $field);

        if (! $fieldDef || ! isset($fieldDef['relationship'], $fieldDef['displayColumn'])) {
            return response()->json([]);
        }

        $model = new ($moduleClass::getModel());
        $relation = $model->{$fieldDef['relationship']}();
        $relatedModel = $relation->getRelated();
        $titleColumn = $fieldDef['displayColumn'];

        $query = $relatedModel::query();

        $search = $request->input('search', '');
        if ($search) {
            $query->where($titleColumn, 'LIKE', "%{$search}%");
        }

        $results = $query->limit(50)->get()->map(fn ($record) => [
            'value' => (string) $record->getKey(),
            'label' => $record->{$titleColumn},
        ]);

        return response()->json($results);
    }

    /**
     * @param  array<int, mixed>  $schema
     * @return array<string, mixed>|null
     */
    private function findBelongsToField(array $schema, string $fieldName): ?array
    {
        foreach ($schema as $component) {
            if (is_array($component) && ($component['type'] ?? null) === 'belongsTo' && ($component['name'] ?? null) === $fieldName) {
                return $component;
            }
            // Search nested sections
            if (is_array($component) && isset($component['schema'])) {
                $found = $this->findBelongsToField($component['schema'], $fieldName);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    public function create(string $module): Response
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        abort_unless($this->canCreate($moduleClass), 403);

        $formSchema = $moduleClass::form(new Form)->toSchema('create');

        return Inertia::render('Studio::CreateRecord', [
            'panel' => $manager->schema(),
            'module' => $moduleClass::meta(),
            'formSchema' => $formSchema,
        ]);
    }

    public function store(Request $request, string $module): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        abort_unless($this->canCreate($moduleClass), 403);

        $schema = $moduleClass::form(new Form)->toSchema('create')['schema'];
        $rules = $this->extractValidationRules($schema);
        $validated = $request->validate($rules);

        $fields = $this->extractFieldNames($schema);
        $data = $this->sanitizeFormData(array_intersect_key($validated, array_flip($fields)));

        $moduleClass::getModel()::create($data);

        $panel = $manager->getCurrentPanel();

        return redirect($panel->getPath().'/'.$module)
            ->with('success', $moduleClass::getLabel().' created.');
    }

    public function show(string $module, mixed $record): Response
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canView($moduleClass, $record), 403);

        $details = $moduleClass::detail();

        // If no detail() defined, fall back to the form schema in read-only mode
        if (empty($details)) {
            $formSchema = $moduleClass::form(new Form)->toSchema('edit');
            $formSchema['readonly'] = true;

            return Inertia::render('Studio::ViewRecord', [
                'panel' => $manager->schema(),
                'module' => $moduleClass::meta(),
                'record' => $record,
                'formSchema' => $formSchema,
            ]);
        }

        $detailSchema = array_map(
            fn (mixed $detail) => $detail->toArray(),
            $details,
        );

        return Inertia::render('Studio::ViewRecord', [
            'panel' => $manager->schema(),
            'module' => $moduleClass::meta(),
            'record' => $record,
            'detailSchema' => $detailSchema,
        ]);
    }

    public function edit(string $module, mixed $record): Response
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canUpdate($moduleClass, $record), 403);

        $formSchema = $moduleClass::form(new Form)->toSchema('edit');

        return Inertia::render('Studio::EditRecord', [
            'panel' => $manager->schema(),
            'module' => $moduleClass::meta(),
            'record' => $record,
            'formSchema' => $formSchema,
        ]);
    }

    public function update(Request $request, string $module, mixed $record): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canUpdate($moduleClass, $record), 403);

        $schema = $moduleClass::form(new Form)->toSchema('edit')['schema'];
        $rules = $this->extractValidationRules($schema);
        $validated = $request->validate($rules);

        $fields = $this->extractFieldNames($schema);
        $record->update($this->sanitizeFormData(array_intersect_key($validated, array_flip($fields))));

        return redirect()->back()->with('success', $moduleClass::getLabel().' updated.');
    }

    public function destroy(string $module, mixed $record): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canDelete($moduleClass, $record), 403);

        $record->delete();

        $panel = $manager->getCurrentPanel();

        return redirect($panel->getPath().'/'.$module)
            ->with('success', $moduleClass::getLabel().' deleted.');
    }

    /**
     * Apply authorization flags to table row actions based on module policies.
     *
     * For list views, per-record checks are not possible, so we use ability-level
     * checks (viewAny implies view is available, etc.).
     *
     * @param  array<int, array<string, mixed>>  $actions
     * @return array<int, array<string, mixed>>
     */
    /**
     * Extract field names from a serialized form schema (recursive).
     *
     * @param  array<int, array<string, mixed>>  $schema
     * @return array<string>
     */
    /**
     * Clean form data — convert empty arrays/objects to null, strip non-scalar junk.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    /**
     * Extract Laravel validation rules from a serialized form schema.
     *
     * @param  array<int, array<string, mixed>>  $schema
     * @return array<string, array<string>>
     */
    protected function extractValidationRules(array $schema): array
    {
        $rules = [];

        foreach ($schema as $component) {
            // Recurse into sections/layouts
            if (isset($component['schema']) && is_array($component['schema'])) {
                $rules = array_merge($rules, $this->extractValidationRules($component['schema']));

                continue;
            }

            $name = $component['name'] ?? null;
            $type = $component['type'] ?? null;

            if (! $name || in_array($type, ['hidden', 'placeholder'], true)) {
                continue;
            }

            $fieldRules = ['nullable'];

            if (! empty($component['required'])) {
                $fieldRules = ['required'];
            }

            // Type-based rules
            match ($type) {
                'email' => $fieldRules[] = 'email',
                'url' => $fieldRules[] = 'url',
                'number', 'stepper', 'money', 'percent' => $fieldRules[] = 'numeric',
                'toggle', 'checkbox' => $fieldRules[] = 'boolean',
                default => null,
            };

            // Constraints
            if (isset($component['maxLength']) && $component['maxLength']) {
                $fieldRules[] = 'max:'.$component['maxLength'];
            }
            if (isset($component['min']) && $component['min'] !== null) {
                $fieldRules[] = 'min:'.$component['min'];
            }
            if (isset($component['max']) && $component['max'] !== null) {
                $fieldRules[] = 'max:'.$component['max'];
            }

            $rules[$name] = $fieldRules;
        }

        return $rules;
    }

    protected function sanitizeFormData(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value) && empty($value)) {
                return null;
            }
            if (is_array($value)) {
                return json_encode($value);
            }

            return $value;
        }, $data);
    }

    protected function extractFieldNames(array $schema): array
    {
        $names = [];
        foreach ($schema as $component) {
            if (isset($component['name']) && $component['type'] !== 'placeholder') {
                $names[] = $component['name'];
            }
            if (isset($component['schema']) && is_array($component['schema'])) {
                $names = array_merge($names, $this->extractFieldNames($component['schema']));
            }
        }

        return $names;
    }

    protected function applyActionAuthorization(array $actions, string $moduleClass): array
    {
        return array_map(function (array $action) use ($moduleClass) {
            $action['authorized'] = match ($action['type'] ?? null) {
                'view' => $this->canViewAny($moduleClass),
                'edit' => $this->canViewAny($moduleClass),
                'delete' => $this->canViewAny($moduleClass),
                default => $action['authorized'] ?? true,
            };

            return $action;
        }, $actions);
    }
}
