<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Navigation\NavigationBuilder;
use JsonSerializable;

abstract class Panel implements JsonSerializable
{
    use HasSchema;

    protected string $path = '/admin';

    protected string $brandName = 'Admin';

    protected ?string $brandLogo = null;

    protected ?string $brandLogoCollapsed = null;

    protected ?string $favicon = null;

    protected string $guard = 'web';

    /** @var array<string> */
    protected array $middleware = ['web'];

    /** @var array<class-string<Module>> */
    protected array $discoveredModules = [];

    public function theme(): Theme
    {
        return Theme::make();
    }

    public function layout(): LayoutConfig
    {
        return LayoutConfig::make();
    }

    public function canAccess(mixed $user): bool
    {
        return true;
    }

    public function guard(): string
    {
        return $this->guard;
    }

    public function middleware(): array
    {
        return $this->middleware;
    }

    /**
     * Override to explicitly register modules.
     * If empty, auto-discovery fills this.
     *
     * @return array<class-string<Module>>
     */
    public function modules(): array
    {
        return [];
    }

    /**
     * @return array<Navigation\NavigationGroup>
     */
    public function navigationGroups(): array
    {
        return [];
    }

    /**
     * @return array<Widget>
     */
    public function dashboard(): array
    {
        return [];
    }

    /**
     * Override to provide a custom dashboard page class.
     * Auto-discovered from app/Studio/{Panel}/Pages/Dashboard.php
     *
     * @return class-string|null
     */
    public function dashboardPage(): ?string
    {
        return null;
    }

    public function getId(): string
    {
        return str(class_basename(static::class))
            ->kebab()
            ->toString();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBrandName(): string
    {
        return $this->brandName;
    }

    /**
     * Set modules discovered by the adapter's auto-discovery.
     *
     * @param  array<class-string<Module>>  $modules
     */
    public function setDiscoveredModules(array $modules): void
    {
        $this->discoveredModules = $modules;
    }

    /**
     * Get all modules — explicitly registered or auto-discovered.
     *
     * @return array<class-string<Module>>
     */
    public function getModules(): array
    {
        $explicit = $this->modules();

        return ! empty($explicit) ? $explicit : $this->discoveredModules;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $modules = $this->getModules();

        return [
            'id' => $this->getId(),
            'path' => $this->path,
            'brandName' => $this->brandName,
            'brandLogo' => $this->brandLogo,
            'brandLogoCollapsed' => $this->brandLogoCollapsed,
            'favicon' => $this->favicon,
            'theme' => $this->theme()->toArray(),
            'layout' => $this->layout()->toArray(),
            'navigation' => NavigationBuilder::build($this, $modules),
            'modules' => array_map(
                fn (string $module) => $module::meta(),
                $modules,
            ),
            'tenancy' => null,
            'user' => null,
        ];
    }
}
