<?php

namespace InertiaStudio\Fields;

class RichEditorField extends BaseField
{
    protected string $type = 'richEditor';

    /** @var string[]|null */
    protected ?array $toolbarButtons = null;

    /** @param string[] $buttons */
    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'toolbarButtons' => $this->toolbarButtons,
        ];
    }
}
