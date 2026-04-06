<?php

namespace InertiaStudio\Laravel\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use InertiaStudio\Laravel\PanelManager;

class HandleStudioInertiaRequests extends Middleware
{
    protected $rootView = 'studio::app';

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        return [
            ...parent::share($request),
            'studio' => [
                'panel' => $panel?->toArray(),
                'user' => $panel && $request->user($panel->guard()) ? [
                    'name' => $request->user($panel->guard())->name ?? '',
                    'email' => $request->user($panel->guard())->email ?? '',
                    'avatar' => null,
                ] : null,
                'notifications' => fn () => $panel && $request->user($panel->guard())
                    ? array_map(
                        fn ($n) => $n->toArray(),
                        $panel->notifications($request->user($panel->guard())),
                    )
                    : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->pull('success'),
                'error' => fn () => $request->session()->pull('error'),
            ],
        ];
    }
}
