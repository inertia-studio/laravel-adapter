<?php

namespace InertiaStudio\Laravel\Search;

use InertiaStudio\Laravel\PanelManager;

class GlobalSearchProvider
{
    /**
     * Search across all modules in the current panel.
     *
     * @return array<int, array{module: string, label: string, icon: array<string, mixed>, results: array<int, array{id: mixed, title: string, url: string}>}>
     */
    public static function search(PanelManager $manager, string $query, int $limit = 5): array
    {
        $panel = $manager->getCurrentPanel();

        if (! $panel) {
            return [];
        }

        $results = [];

        foreach ($panel->getModules() as $moduleClass) {
            $searchColumns = $moduleClass::globalSearch();

            if (empty($searchColumns)) {
                continue;
            }

            $modelClass = $moduleClass::getModel();
            $titleAttribute = $moduleClass::getRecordTitleAttribute();

            $records = $modelClass::query()
                ->where(function ($q) use ($searchColumns, $query) {
                    foreach ($searchColumns as $column) {
                        $q->orWhere($column, 'LIKE', "%{$query}%");
                    }
                })
                ->limit($limit)
                ->get();

            if ($records->isEmpty()) {
                continue;
            }

            $results[] = [
                'module' => $moduleClass::getSlug(),
                'label' => $moduleClass::getPluralLabel(),
                'icon' => [
                    'name' => $moduleClass::getNavigationIcon(),
                    'provider' => null,
                    'variant' => null,
                ],
                'results' => $records->map(fn ($record) => [
                    'id' => $record->getKey(),
                    'title' => $record->{$titleAttribute},
                    'url' => $panel->getPath().'/'.$moduleClass::getSlug().'/'.$record->getKey(),
                ])->toArray(),
            ];
        }

        return $results;
    }
}
