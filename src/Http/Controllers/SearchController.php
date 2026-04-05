<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InertiaStudio\Laravel\PanelManager;
use InertiaStudio\Laravel\Search\GlobalSearchProvider;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $manager = app(PanelManager::class);

        if (! $query) {
            return response()->json([]);
        }

        $limit = $request->integer('limit', 5);

        $results = GlobalSearchProvider::search($manager, $query, $limit);

        return response()->json($results);
    }
}
