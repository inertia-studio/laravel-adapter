<?php

namespace InertiaStudio\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InertiaStudio\Laravel\PanelManager;
use Symfony\Component\HttpFoundation\Response;

class AuthorizePanelAccess
{
    public function __construct(
        protected PanelManager $manager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $panel = $this->manager->getCurrentPanel();

        if (! $panel || ! $panel->canAccess($request->user($panel->guard()))) {
            abort(403, 'Unauthorized access to this panel.');
        }

        return $next($request);
    }
}
