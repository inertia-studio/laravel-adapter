<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use InertiaStudio\Laravel\PanelManager;
use InertiaStudio\PageActionBuilder;
use InertiaStudio\PageFormBuilder;
use InertiaStudio\PageSchema;
use InertiaStudio\PageTableBuilder;
use InertiaStudio\Widget;

class PanelController extends Controller
{
    public function dashboard(): Response
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        // Check for custom dashboard page
        $dashboardPageClass = $panel?->dashboardPage();

        // Auto-discover from Pages/Dashboard.php
        if (! $dashboardPageClass) {
            $panelClass = get_class($panel);
            $namespace = substr($panelClass, 0, strrpos($panelClass, '\\'));
            $candidate = $namespace.'\\Pages\\Dashboard';

            if (class_exists($candidate)) {
                $dashboardPageClass = $candidate;
            }
        }

        if ($dashboardPageClass && class_exists($dashboardPageClass)) {
            $page = new $dashboardPageClass;
            $pageData = $page->toArray();

            // Resolve queries and form actions
            $pageData['schema'] = $this->resolvePageSchema($page->schema(), $pageData['schema'], $panel, 'dashboard');

            return Inertia::render('Studio::Dashboard', [
                'dashboardPage' => $pageData,
            ]);
        }

        return Inertia::render('Studio::Dashboard');
    }

    public function customPage(string $page): Response
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        $pageInstance = $this->resolvePageInstance($panel, $page);

        // Check access
        if (method_exists($pageInstance, 'canAccess')) {
            $guard = $panel->guard();
            abort_unless($pageInstance->canAccess(request()->user($guard)), 403);
        }

        $pageData = $pageInstance->toArray();

        // Resolve queries and form actions
        $pageData['schema'] = $this->resolvePageSchema($pageInstance->schema(), $pageData['schema'], $panel, $page);

        return Inertia::render('Studio::Dashboard', [
            'dashboardPage' => $pageData,
        ]);
    }

    /**
     * Handle a page form submission (closure or controller action).
     */
    public function handlePageForm(Request $request, string $page, string $formKey): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        $pageInstance = $this->resolvePageInstance($panel, $page);

        // Check access
        if (method_exists($pageInstance, 'canAccess')) {
            $guard = $panel->guard();
            abort_unless($pageInstance->canAccess($request->user($guard)), 403);
        }

        // Find the form builder by key
        $formBuilder = $this->findFormBuilder($pageInstance->schema(), $formKey);

        abort_unless($formBuilder !== null, 404, "Form [{$formKey}] not found on page [{$page}].");

        $action = $formBuilder->getAction();

        if ($action instanceof Closure) {
            return app()->call($action, ['request' => $request]);
        }

        if (is_array($action)) {
            return app()->call($action, ['request' => $request]);
        }

        abort(404, "Form [{$formKey}] has no handler.");
    }

    /**
     * Handle a page action button click (closure or controller action).
     */
    public function handlePageAction(Request $request, string $page, string $actionKey): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        $pageInstance = $this->resolvePageInstance($panel, $page);

        // Check access
        if (method_exists($pageInstance, 'canAccess')) {
            $guard = $panel->guard();
            abort_unless($pageInstance->canAccess($request->user($guard)), 403);
        }

        $actionBuilder = $this->findActionBuilder($pageInstance->schema(), $actionKey);

        abort_unless($actionBuilder !== null, 404, "Action [{$actionKey}] not found on page [{$page}].");

        $action = $actionBuilder->getAction();

        if ($action instanceof Closure) {
            return app()->call($action, ['request' => $request]);
        }

        if (is_array($action)) {
            return app()->call($action, ['request' => $request]);
        }

        abort(404, "Action [{$actionKey}] has no handler.");
    }

    /**
     * Resolve a page class from its slug.
     */
    private function resolvePageInstance(mixed $panel, string $page): mixed
    {
        $className = str($page)->studly()->toString();

        $panelClass = get_class($panel);
        $namespace = substr($panelClass, 0, strrpos($panelClass, '\\'));
        $pageClass = $namespace.'\\Pages\\'.$className;

        if (! class_exists($pageClass)) {
            abort(404, "Page [{$page}] not found.");
        }

        return new $pageClass;
    }

    /**
     * Find a PageFormBuilder by its key in the schema tree.
     */
    private function findFormBuilder(array $schema, string $formKey): ?PageFormBuilder
    {
        foreach ($schema as $item) {
            if ($item instanceof PageFormBuilder && $item->getKey() === $formKey) {
                return $item;
            }

            // Recurse into containers
            $children = $this->getSchemaChildren($item);
            if (! empty($children)) {
                $found = $this->findFormBuilder($children, $formKey);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Find a PageActionBuilder by its key in the schema tree.
     */
    private function findActionBuilder(array $schema, string $actionKey): ?PageActionBuilder
    {
        foreach ($schema as $item) {
            if ($item instanceof PageActionBuilder && $item->getKey() === $actionKey) {
                return $item;
            }

            // Recurse into containers
            $children = $this->getSchemaChildren($item);
            if (! empty($children)) {
                $found = $this->findActionBuilder($children, $actionKey);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Get child schema objects from a container item.
     *
     * @return array<mixed>
     */
    private function getSchemaChildren(mixed $item): array
    {
        $propName = match (true) {
            $item instanceof PageSchema => 'children',
            $item instanceof Widget => 'schema',
            default => null,
        };

        if ($propName && property_exists($item, $propName)) {
            $ref = new \ReflectionProperty($item, $propName);
            $ref->setAccessible(true);

            return $ref->getValue($item);
        }

        return [];
    }

    /**
     * Resolve queries and form action URLs in page schema.
     *
     * @param  array<mixed>  $sourceSchema  Original PHP objects
     * @param  array<mixed>  $serialized  Serialized schema
     * @return array<mixed>
     */
    private function resolvePageSchema(array $sourceSchema, array $serialized, mixed $panel, string $pageSlug): array
    {
        foreach ($sourceSchema as $i => $item) {
            // Inline table — resolve query to flat array
            if ($item instanceof PageTableBuilder && $item->getQuery()) {
                $queryResult = ($item->getQuery())();

                if ($queryResult instanceof Builder) {
                    $defaultSort = $serialized[$i]['defaultSort'] ?? null;
                    if ($defaultSort) {
                        $queryResult->orderBy($defaultSort['column'], $defaultSort['direction'] ?? 'asc');
                    }

                    $serialized[$i]['data'] = $queryResult->get()->toArray();
                } elseif ($queryResult instanceof Collection) {
                    $serialized[$i]['data'] = $queryResult->toArray();
                } elseif (is_array($queryResult)) {
                    $serialized[$i]['data'] = $queryResult;
                } else {
                    $serialized[$i]['data'] = $queryResult->toArray();
                }
            }

            // Inline form — resolve closure/array actions to URLs
            if ($item instanceof PageFormBuilder) {
                $action = $item->getAction();

                if ($action instanceof Closure || is_array($action)) {
                    $url = $panel->getPath().'/pages/'.$pageSlug.'/forms/'.$item->getKey();
                    $serialized[$i] = $item->resolvedActionUrl($url);
                }
            }

            // Action button — resolve closure/array actions to URLs
            if ($item instanceof PageActionBuilder) {
                $action = $item->getAction();

                if ($action instanceof Closure || is_array($action)) {
                    $url = $panel->getPath().'/pages/'.$pageSlug.'/actions/'.$item->getKey();
                    $serialized[$i] = $item->resolvedUrl($url);
                }
            }

            // Recurse into grids/cards/sections
            if (isset($serialized[$i]['schema']) && is_array($serialized[$i]['schema'])) {
                $children = $this->getSchemaChildren($item);

                if (! empty($children)) {
                    $serialized[$i]['schema'] = $this->resolvePageSchema($children, $serialized[$i]['schema'], $panel, $pageSlug);
                }
            }
        }

        return $serialized;
    }

    public function profile(Request $request): Response
    {
        $panel = app(PanelManager::class)->getCurrentPanel();
        $user = $request->user($panel->guard());

        return Inertia::render('Studio::Profile', [
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => null,
                'created_at' => $user->created_at?->toISOString(),
            ],
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();
        $user = $request->user($panel->guard());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();
        $user = $request->user($panel->guard());

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:'.$panel->guard()],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated.');
    }
}
