<?php

namespace InertiaStudio\Fields;

class KeyValueField extends BaseField
{
    protected string $type = 'keyValue';

    protected ?string $keyLabel = null;

    protected ?string $valueLabel = null;

    protected bool $addable = true;

    protected bool $deletable = true;

    public function keyLabel(string $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function addable(bool $condition = true): static
    {
        $this->addable = $condition;

        return $this;
    }

    public function deletable(bool $condition = true): static
    {
        $this->deletable = $condition;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'keyLabel' => $this->keyLabel,
            'valueLabel' => $this->valueLabel,
            'addable' => $this->addable,
            'deletable' => $this->deletable,
        ];
    }
}
