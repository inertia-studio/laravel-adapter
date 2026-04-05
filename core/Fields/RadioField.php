<?php

namespace InertiaStudio\Fields;

class RadioField extends BaseField
{
    protected string $type = 'radio';

    /** @var array<int|string, string> */
    protected array $options = [];

    protected bool $inline = false;

    /** @param array<int|string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function inline(): static
    {
        $this->inline = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'options' => $this->options,
            'inline' => $this->inline,
        ];
    }
}
