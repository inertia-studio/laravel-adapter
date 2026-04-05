<?php

namespace InertiaStudio\Laravel;

use InertiaStudio\Module;
use InertiaStudio\Panel;

class PanelManager
{
    /** @var array<string, Panel> */
    protected array $panels = [];

    protected ?Panel $currentPanel = null;

    public function register(Panel $panel): void
    {
        $this->panels[$panel->getId()] = $panel;
    }

    public function getPanel(string $id): Panel
    {
        return $this->panels[$id] ?? throw new \InvalidArgumentException("Panel [{$id}] not found.");
    }

    /**
     * @return array<string, Panel>
     */
    public function getPanels(): array
    {
        return $this->panels;
    }

    public function getCurrentPanel(): ?Panel
    {
        return $this->currentPanel;
    }

    public function setCurrentPanel(Panel $panel): void
    {
        $this->currentPanel = $panel;
    }

    /**
     * Resolve a module class from its slug within the current panel.
     *
     * @return class-string<Module>
     */
    public function resolveModule(string $slug): string
    {
        $panel = $this->currentPanel ?? throw new \RuntimeException('No active panel.');

        foreach ($panel->getModules() as $module) {
            if ($module::getSlug() === $slug) {
                return $module;
            }
        }

        throw new \InvalidArgumentException("Module [{$slug}] not found in panel [{$panel->getId()}].");
    }

    /**
     * Get the current panel's serialized schema.
     *
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        $panel = $this->currentPanel ?? throw new \RuntimeException('No active panel.');

        return $panel->toArray();
    }
}
