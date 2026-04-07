<?php

namespace InertiaStudio\Fields;

class MarkdownEditorField extends BaseField
{
    protected string $type = 'markdownEditor';

    protected int $minLines = 8;

    protected int $maxLines = 30;

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
            'minLines' => $this->minLines,
            'maxLines' => $this->maxLines,
        ];
    }
}
