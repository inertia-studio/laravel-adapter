<?php

namespace InertiaStudio\Fields;

class TagsField extends BaseField
{
    protected string $type = 'tags';

    /** @var string[]|null */
    protected ?array $suggestions = null;

    protected ?string $separator = null;

    /** @param string[] $suggestions */
    public function suggestions(array $suggestions): static
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    public function separator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'suggestions' => $this->suggestions,
            'separator' => $this->separator,
        ];
    }
}
