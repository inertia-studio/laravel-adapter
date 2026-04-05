<?php

namespace InertiaStudio\Fields;

class RepeaterField extends BaseField
{
    protected string $type = 'repeater';

    /** @var array<int, BaseField> */
    protected array $schema = [];

    protected bool $addable = true;

    protected bool $deletable = true;

    protected bool $reorderable = true;

    protected ?int $maxItems = null;

    protected ?int $minItems = null;

    protected bool $collapsible = false;

    /** @param array<int, BaseField> $schema */
    public function schema(array $schema): static
    {
        $this->schema = $schema;

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

    public function reorderable(bool $condition = true): static
    {
        $this->reorderable = $condition;

        return $this;
    }

    public function maxItems(int $count): static
    {
        $this->maxItems = $count;

        return $this;
    }

    public function minItems(int $count): static
    {
        $this->minItems = $count;

        return $this;
    }

    public function collapsible(): static
    {
        $this->collapsible = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'schema' => array_map(fn (BaseField $field) => $field->toArray(), $this->schema),
            'addable' => $this->addable,
            'deletable' => $this->deletable,
            'reorderable' => $this->reorderable,
            'maxItems' => $this->maxItems,
            'minItems' => $this->minItems,
            'collapsible' => $this->collapsible,
        ];
    }
}
