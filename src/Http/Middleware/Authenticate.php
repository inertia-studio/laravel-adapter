<?php

namespace InertiaStudio\Laravel\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use InertiaStudio\Laravel\PanelManager;

class Authenticate extends BaseAuthenticate
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Try to get the panel from the manager first
        $panel = app(PanelManager::class)->getCurrentPanel();

        if ($panel) {
            return $panel->getPath().'/login';
        }

        // Fallback: extract panel path from the URL by matching registered panels
        $manager = app(PanelManager::class);

        foreach ($manager->getPanels() as $p) {
            if (str_starts_with($request->getPathInfo(), $p->getPath())) {
                return $p->getPath().'/login';
            }
        }

        return '/login';
    }
}
