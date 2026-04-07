<?php

namespace InertiaStudio\Fields;

class CodeField extends BaseField
{
    protected string $type = 'code';

    protected string $language = 'json';

    protected int $minLines = 8;

    protected int $maxLines = 30;

    public function language(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function minLines(int $lines): static
    {
        $this->minLines = $lines;

        return $this;
    }

    public function maxLines(int $lines): static
    {
        $this->maxLines = $lines;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'language' => $this->language,
            'minLines' => $this->minLines,
            'maxLines' => $this->maxLines,
        ];
    }
}
