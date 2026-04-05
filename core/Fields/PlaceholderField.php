<?php

namespace InertiaStudio\Fields;

class PlaceholderField extends BaseField
{
    protected string $type = 'placeholder';

    protected ?string $content = null;

    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'content' => $this->content,
        ];
    }
}
