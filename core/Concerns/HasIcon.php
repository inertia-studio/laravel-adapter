<?php

namespace InertiaStudio\Concerns;

trait HasIcon
{
    protected ?string $iconName = null;

    protected ?string $iconProvider = null;

    protected ?string $iconVariant = null;

    public function icon(string $name, ?string $provider = null): static
    {
        if (str_contains($name, ':')) {
            [$name, $this->iconVariant] = explode(':', $name, 2);
        }

        $this->iconName = $name;
        $this->iconProvider = $provider;

        return $this;
    }

    /**
     * @return array{name: string, provider: string|null, variant: string|null}|null
     */
    public function getIconSchema(): ?array
    {
        if ($this->iconName === null) {
            return null;
        }

        return [
            'name' => $this->iconName,
            'provider' => $this->iconProvider,
            'variant' => $this->iconVariant,
        ];
    }
}
