<?php

namespace InertiaStudio\Laravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InertiaStudio\Laravel\Commands\InstallCommand;
use InertiaStudio\Laravel\Commands\MakeModuleCommand;
use InertiaStudio\Laravel\Commands\MakePageCommand;
use InertiaStudio\Laravel\Commands\MakePanelCommand;
use InertiaStudio\Laravel\Commands\MakeRelationCommand;
use InertiaStudio\Laravel\Commands\MakeWidgetCommand;
use InertiaStudio\Laravel\Commands\PublishCommand;
use InertiaStudio\Laravel\Discovery\PanelDiscovery;
use InertiaStudio\Laravel\Http\Middleware\HandleStudioInertiaRequests;
use InertiaStudio\Laravel\Http\Middleware\ResolvePanelMiddleware;

class InertiaStudioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/studio.php', 'studio');

        $this->app->singleton(PanelManager::class, function () {
            $manager = new PanelManager;

            $discovered = PanelDiscovery::discover(
                app_path('Studio'),
                'App\\Studio',
            );

            foreach ($discovered as $panelClass => $discovery) {
                $panel = new $panelClass;
                $panel->setDiscoveredModules($discovery['modules']);
                $panel->setDiscoveredPages($discovery['pages']);
                $manager->register($panel);
            }

            return $manager;
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'studio');

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/studio.php' => config_path('studio.php'),
            ], 'studio-config');

            $this->publishes([
                __DIR__.'/../resources/logo.svg' => public_path('vendor/studio/logo.svg'),
                __DIR__.'/../resources/logo-wordmark.svg' => public_path('vendor/studio/logo-wordmark.svg'),
                __DIR__.'/../resources/logo-wordmark-dark.svg' => public_path('vendor/studio/logo-wordmark-dark.svg'),
            ], 'studio-assets');

            $this->commands([
                InstallCommand::class,
                MakePanelCommand::class,
                MakeModuleCommand::class,
                MakeRelationCommand::class,
                MakePageCommand::class,
                MakeWidgetCommand::class,
                PublishCommand::class,
            ]);
        }
    }

    protected function registerRoutes(): void
    {
        $manager = $this->app->make(PanelManager::class);

        foreach ($manager->getPanels() as $panel) {
            $guard = $panel->guard();

            Route::prefix($panel->getPath())
                ->middleware([
                    'web',
                    ResolvePanelMiddleware::class.':'.$panel->getId(),
                    HandleStudioInertiaRequests::class,
                ])
                ->group(function () use ($guard) {
                    $routeFile = __DIR__.'/../routes/studio.php';

                    // Replace {guard} placeholder in auth middleware
                    $this->app->booted(function () {
                        // Guard is resolved at runtime via panel
                    });

                    require $routeFile;
                });
        }
    }
}
