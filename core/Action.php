<?php

namespace InertiaStudio;

use Closure;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Action implements JsonSerializable
{
    use HasIcon;
    use HasSchema;

    protected string $type;

    protected string $name;

    protected ?string $label = null;

    protected ?string $color = null;

    protected bool $requiresConfirmation = false;

    protected ?string $confirmationHeading = null;

    protected ?string $confirmationMessage = null;

    protected string|Closure|null $url = null;

    /** @var array<int, mixed>|null */
    protected ?array $form = null;

    protected bool $authorized = true;

    protected ?Closure $action = null;

    public static function create(): static
    {
        $action = new static;
        $action->type = 'create';
        $action->name = 'create';
        $action->label = 'Create';
        $action->icon('plus');

        return $action;
    }

    public static function view(): static
    {
        $action = new static;
        $action->type = 'view';
        $action->name = 'view';
        $action->label = 'View';
        $action->icon('eye');

        return $action;
    }

    public static function edit(): static
    {
        $action = new static;
        $action->type = 'edit';
        $action->name = 'edit';
        $action->label = 'Edit';
        $action->icon('pencil');

        return $action;
    }

    public static function delete(): static
    {
        $action = new static;
        $action->type = 'delete';
        $action->name = 'delete';
        $action->label = 'Delete';
        $action->icon('trash');
        $action->requiresConfirmation = true;

        return $action;
    }

    public static function bulkDelete(): static
    {
        $action = new static;
        $action->type = 'bulkDelete';
        $action->name = 'bulkDelete';
        $action->label = 'Delete Selected';
        $action->icon('trash');
        $action->requiresConfirmation = true;

        return $action;
    }

    public static function export(): static
    {
        $action = new static;
        $action->type = 'export';
        $action->name = 'export';
        $action->label = 'Export';
        $action->icon('arrow-down-tray');

        return $action;
    }

    public static function custom(string $name): static
    {
        $action = new static;
        $action->type = 'custom';
        $action->name = $name;
        $action->label = str($name)
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();

        return $action;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function requiresConfirmation(?string $heading = null, ?string $message = null): static
    {
        $this->requiresConfirmation = true;

        if ($heading !== null) {
            $this->confirmationHeading = $heading;
        }

        if ($message !== null) {
            $this->confirmationMessage = $message;
        }

        return $this;
    }

    public function modalHeading(string $heading): static
    {
        $this->confirmationHeading = $heading;

        return $this;
    }

    public function modalDescription(string $description): static
    {
        $this->confirmationMessage = $description;

        return $this;
    }

    public function url(string|Closure $url): static
    {
        $this->url = $url;

        return $this;
    }

    /** @param array<int, mixed> $form */
    public function form(array $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function action(Closure $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function authorized(bool $authorized): static
    {
        $this->authorized = $authorized;

        return $this;
    }

    public function getAction(): ?Closure
    {
        return $this->action;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->name)
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->getLabel(),
            'icon' => $this->getIconSchema(),
            'color' => $this->color,
            'requiresConfirmation' => $this->requiresConfirmation,
            'confirmationHeading' => $this->confirmationHeading,
            'confirmationMessage' => $this->confirmationMessage,
            'url' => $this->url instanceof Closure ? null : $this->url,
            'form' => $this->form,
            'authorized' => $this->authorized,
        ];
    }
}
