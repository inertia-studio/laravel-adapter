<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use InertiaStudio\Form;
use InertiaStudio\Laravel\Concerns\HasAuthorization;
use InertiaStudio\Laravel\PanelManager;
use InertiaStudio\Laravel\Table\QueryBuilder;
use InertiaStudio\Table;

class SimpleModuleController extends Controller
{
    use HasAuthorization;

    public function index(Request $request, string $module): Response
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        abort_unless($this->canViewAny($moduleClass), 403);

        $table = $moduleClass::table(new Table);
        $query = $moduleClass::getModel()::query();

        $query = QueryBuilder::apply($query, $table, [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'filters' => $request->input('filters', []),
        ]);

        $perPage = $request->integer('perPage', 15);
        $records = $query->paginate($perPage)->withQueryString();

        $tableSchema = $table->toSchema();
        $simpleFormSchema = $moduleClass::form(new Form)->toSchema('create');

        return Inertia::render('Studio::ListRecords', [
            'panel' => $manager->schema(),
            'module' => $moduleClass::meta(),
            'tableSchema' => $tableSchema,
            'records' => $records,
            'tabs' => $moduleClass::tabs(),
            'simpleFormSchema' => $simpleFormSchema,
        ]);
    }

    public function store(Request $request, string $module): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        abort_unless($this->canCreate($moduleClass), 403);

        $moduleClass::getModel()::create($request->all());

        return redirect()->back()->with('success', $moduleClass::getLabel().' created.');
    }

    public function update(Request $request, string $module, mixed $record): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canUpdate($moduleClass, $record), 403);

        $record->update($request->all());

        return redirect()->back()->with('success', $moduleClass::getLabel().' updated.');
    }

    public function destroy(Request $request, string $module, mixed $record): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $record = $moduleClass::getModel()::findOrFail($record);

        abort_unless($this->canDelete($moduleClass, $record), 403);

        $record->delete();

        return redirect()->back()->with('success', $moduleClass::getLabel().' deleted.');
    }
}
