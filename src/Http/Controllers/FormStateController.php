<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InertiaStudio\Form;
use InertiaStudio\Laravel\PanelManager;

class FormStateController extends Controller
{
    public function evaluate(Request $request, string $module): JsonResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $operation = $request->input('operation', 'create');

        $formSchema = $moduleClass::form(new Form)->toSchema($operation);

        return response()->json([
            'formSchema' => $formSchema,
        ]);
    }
}
