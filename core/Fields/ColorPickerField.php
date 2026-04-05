<?php

namespace InertiaStudio\Fields;

class ColorPickerField extends BaseField
{
    protected string $type = 'colorPicker';

    /** @var string[]|null */
    protected ?array $swatches = null;

    protected ?string $format = null;

    /** @param string[] $swatches */
    public function swatches(array $swatches): static
    {
        $this->swatches = $swatches;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'swatches' => $this->swatches,
            'format' => $this->format,
        ];
    }
}
