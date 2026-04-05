<?php

namespace InertiaStudio\Fields;

use InertiaStudio\Concerns\HasColumns;
use InertiaStudio\Concerns\HasIcon;
use InertiaStudio\Concerns\HasLabel;
use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Concerns\HasState;
use InertiaStudio\Concerns\HasVisibility;
use JsonSerializable;

abstract class BaseField implements JsonSerializable
{
    use HasColumns;
    use HasIcon;
    use HasLabel;
    use HasSchema;
    use HasState;
    use HasVisibility;

    protected string $type;

    protected ?string $placeholder = null;

    protected ?string $hint = null;

    protected ?string $helperText = null;

    protected ?string $prefix = null;

    protected ?string $suffix = null;

    public function __construct(
        protected string $name,
    ) {}

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function helperText(string $helperText): static
    {
        $this->helperText = $helperText;

        return $this;
    }

    public function prefix(string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
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
            'placeholder' => $this->placeholder,
            'hint' => $this->hint,
            'helperText' => $this->helperText,
            'required' => $this->isRequired(),
            'disabled' => $this->isDisabled(),
            'hidden' => false,
            'columnSpan' => $this->getColumnSpan(),
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'default' => $this->default,
        ];
    }
}
