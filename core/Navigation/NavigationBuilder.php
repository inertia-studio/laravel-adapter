<?php

namespace InertiaStudio\Navigation;

use InertiaStudio\Module;
use InertiaStudio\Panel;

class NavigationBuilder
{
    /**
     * Build the navigation tree for a panel.
     *
     * @param  array<class-string<Module>>  $modules
     * @return array<array<string, mixed>>
     */
    public static function build(Panel $panel, array $modules): array
    {
        $groups = $panel->navigationGroups();

        if (! empty($groups)) {
            return static::buildFromGroups($panel, $groups, $modules);
        }

        return static::buildFlat($panel, $modules);
    }

    /**
     * @param  array<NavigationGroup>  $groups
     * @param  array<class-string<Module>>  $modules
     * @return array<array<string, mixed>>
     */
    protected static function buildFromGroups(Panel $panel, array $groups, array $modules): array
    {
        return array_values(array_filter(array_map(function (NavigationGroup $group) use ($panel) {
            $data = $group->toArray();
            $data['items'] = array_values(array_filter(array_map(function (mixed $item) use ($panel) {
                // Raw array items (custom pages)
                if (is_array($item)) {
                    // Skip hidden items
                    if (isset($item['visible']) && $item['visible'] === false) {
                        return null;
                    }

                    return (new NavigationItem(
                        label: $item['label'] ?? '',
                        icon: isset($item['icon']) ? [
                            'name' => $item['icon'],
                            'provider' => null,
                            'variant' => null,
                        ] : null,
                        url: $item['url'] ?? '#',
                        badge: $item['badge'] ?? null,
                        badgeColor: $item['badgeColor'] ?? 'info',
                    ))->toArray();
                }

                // Module class string
                return static::moduleToItem($panel, $item);
            }, $group->getItems())));

            // Hide empty groups
            if (empty($data['items'])) {
                return null;
            }

            return $data;
        }, $groups)));
    }

    /**
     * @param  array<class-string<Module>>  $modules
     * @return array<array<string, mixed>>
     */
    protected static function buildFlat(Panel $panel, array $modules): array
    {
        $sorted = $modules;
        usort($sorted, fn (string $a, string $b) => $a::getNavigationSort() <=> $b::getNavigationSort());

        // Group modules by their $navigationGroup
        $grouped = [];
        $ungrouped = [];

        foreach ($sorted as $module) {
            $groupName = $module::getNavigationGroup();
            if ($groupName !== null) {
                $grouped[$groupName][] = $module;
            } else {
                $ungrouped[] = $module;
            }
        }

        $result = [];

        // Ungrouped modules go into a default group
        if (! empty($ungrouped)) {
            $result[] = [
                'label' => null,
                'icon' => null,
                'collapsible' => false,
                'collapsed' => false,
                'items' => array_map(
                    fn (string $module) => static::moduleToItem($panel, $module),
                    $ungrouped,
                ),
            ];
        }

        // Named groups
        foreach ($grouped as $groupName => $groupModules) {
            $result[] = [
                'label' => $groupName,
                'icon' => null,
                'collapsible' => false,
                'collapsed' => false,
                'items' => array_map(
                    fn (string $module) => static::moduleToItem($panel, $module),
                    $groupModules,
                ),
            ];
        }

        return $result;
    }

    /**
     * @param  class-string<Module>  $module
     * @return array<string, mixed>
     */
    protected static function moduleToItem(Panel $panel, string $module): array
    {
        return (new NavigationItem(
            label: $module::getNavigationLabel(),
            icon: [
                'name' => $module::getNavigationIcon(),
                'provider' => null,
                'variant' => null,
            ],
            url: $panel->getPath().'/'.$module::getSlug(),
            badge: $module::navigationBadge(),
            badgeColor: $module::navigationBadgeColor(),
        ))->toArray();
    }
}
