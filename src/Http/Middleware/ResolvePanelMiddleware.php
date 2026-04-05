<?php

namespace InertiaStudio\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InertiaStudio\Laravel\PanelManager;
use Symfony\Component\HttpFoundation\Response;

class ResolvePanelMiddleware
{
    public function __construct(
        protected PanelManager $manager,
    ) {}

    public function handle(Request $request, Closure $next, string $panelId): Response
    {
        $panel = $this->manager->getPanel($panelId);

        $this->manager->setCurrentPanel($panel);

        return $next($request);
    }
}
