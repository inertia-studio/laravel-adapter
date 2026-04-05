<?php

namespace InertiaStudio\Fields;

class CheckboxListField extends BaseField
{
    protected string $type = 'checkboxList';

    /** @var array<int|string, string> */
    protected array $options = [];

    protected ?int $columns = null;

    protected bool $searchable = false;

    /** @param array<int|string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function searchable(): static
    {
        $this->searchable = true;

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
            'columns' => $this->columns,
            'searchable' => $this->searchable,
        ];
    }
}
