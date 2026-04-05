<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InertiaStudio\Laravel\PanelManager;

class RelationController extends Controller
{
    public function index(Request $request, string $module, mixed $record, string $relation): JsonResponse
    {
        $manager = app(PanelManager::class);
        $moduleClass = $manager->resolveModule($module);

        $parentRecord = $moduleClass::getModel()::findOrFail($record);

        $relationClass = collect($moduleClass::relations())
            ->first(fn (string $class) => $class::getRelationshipName() === $relation);

        if (! $relationClass) {
            abort(404, "Relation [{$relation}] not found on module [{$module}].");
        }

        $perPage = $request->integer('perPage', 15);
        $relatedRecords = $parentRecord->{$relation}()->paginate($perPage);

        return response()->json([
            'relation' => $relation,
            'records' => $relatedRecords,
        ]);
    }
}
