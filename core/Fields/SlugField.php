<?php

namespace InertiaStudio\Fields;

class SlugField extends BaseField
{
    protected string $type = 'slug';

    protected ?string $from = null;

    public function from(string $field): static
    {
        $this->from = $field;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'from' => $this->from,
        ];
    }
}
