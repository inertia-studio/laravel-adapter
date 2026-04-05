<?php

namespace InertiaStudio\Fields;

class BelongsToField extends BaseField
{
    protected string $type = 'belongsTo';

    protected ?string $relation = null;

    protected ?string $displayColumn = null;

    protected bool $searchable = false;

    protected bool $preload = false;

    protected bool $multiple = false;

    /** @var array<int, BaseField>|null */
    protected ?array $createForm = null;

    public function relationship(string $relation, string $displayColumn): static
    {
        $this->relation = $relation;
        $this->displayColumn = $displayColumn;

        return $this;
    }

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    public function preload(): static
    {
        $this->preload = true;

        return $this;
    }

    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    /** @param array<int, BaseField> $schema */
    public function createForm(array $schema): static
    {
        $this->createForm = $schema;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'relationship' => $this->relation,
            'displayColumn' => $this->displayColumn,
            'searchable' => $this->searchable,
            'preload' => $this->preload,
            'multiple' => $this->multiple,
            'serverSearch' => true,
            'createForm' => $this->createForm !== null
                ? array_map(fn (BaseField $field) => $field->toArray(), $this->createForm)
                : null,
        ];
    }
}
