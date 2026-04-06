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

    protected ?string $brandLogoDark = null;

    protected ?string $brandLogoCollapsed = null;

    protected ?string $favicon = null;

    protected string $guard = 'web';

    /** @var array<string> */
    protected array $middleware = ['web'];

    protected bool $registration = false;

    protected bool $passwordReset = true;

    protected bool $emailVerification = false;

    /** Notification polling interval in seconds. 0 = disabled (refresh on navigation only). */
    protected int $notificationPolling = 0;

    /** Show a toast when new notifications arrive via polling. */
    protected bool $notificationToasts = true;

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

    public function hasRegistration(): bool
    {
        return $this->registration;
    }

    public function hasPasswordReset(): bool
    {
        return $this->passwordReset;
    }

    public function hasEmailVerification(): bool
    {
        return $this->emailVerification;
    }

    /**
     * Override to provide a custom login page class.
     *
     * @return class-string|null
     */
    public function loginPage(): ?string
    {
        return null;
    }

    /**
     * Override to provide a custom registration page class.
     *
     * @return class-string|null
     */
    public function registerPage(): ?string
    {
        return null;
    }

    /**
     * Called after a new user registers. Override to customize post-registration behavior.
     */
    public function afterRegistration(mixed $user): void
    {
        //
    }

    /**
     * The model class to use for registration. Defaults to the guard's provider model.
     *
     * @return class-string|null
     */
    public function userModel(): ?string
    {
        return null;
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
     * Return notifications to display in the topbar bell icon.
     * Override this to pull from your database, cache, or notification system.
     *
     * @return array<StudioNotification>
     */
    public function notifications(mixed $user): array
    {
        return [];
    }

    /**
     * Called when a user marks a notification as read.
     * Override to handle dismissal in your database/notification system.
     */
    public function markNotificationRead(mixed $user, string $notificationId): void
    {
        //
    }

    /**
     * Called when a user marks all notifications as read.
     */
    public function markAllNotificationsRead(mixed $user): void
    {
        //
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
            'brandLogoDark' => $this->brandLogoDark,
            'brandLogoCollapsed' => $this->brandLogoCollapsed,
            'favicon' => $this->favicon,
            'theme' => $this->theme()->toArray(),
            'layout' => $this->layout()->toArray(),
            'navigation' => NavigationBuilder::build($this, $modules),
            'modules' => array_map(
                fn (string $module) => $module::meta(),
                $modules,
            ),
            'auth' => [
                'registration' => $this->hasRegistration(),
                'passwordReset' => $this->hasPasswordReset(),
                'emailVerification' => $this->hasEmailVerification(),
            ],
            'notificationPolling' => $this->notificationPolling,
            'notificationToasts' => $this->notificationToasts,
            'tenancy' => null,
            'user' => null,
        ];
    }
}
