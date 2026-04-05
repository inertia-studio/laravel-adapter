<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InertiaStudio\Laravel\PanelManager;

class ActionController extends Controller
{
    public function execute(Request $request, string $module, string $action): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        if ($action === 'delete') {
            $record = $moduleClass::getModel()::findOrFail($request->input('id'));
            $record->delete();

            return redirect()->back()->with('success', $moduleClass::getLabel().' deleted.');
        }

        // Placeholder for custom action execution
        return redirect()->back()->with('success', 'Action executed.');
    }

    public function executeBulk(Request $request, string $module, string $action): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $ids = $request->input('ids', []);

        if ($action === 'bulkDelete') {
            $moduleClass::getModel()::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', count($ids).' records deleted.');
        }

        // Placeholder for custom bulk action execution
        return redirect()->back()->with('success', 'Bulk action executed.');
    }
}
