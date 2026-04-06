<?php

namespace InertiaStudio\Fields;

class MaskedField extends BaseField
{
    protected string $type = 'masked';

    /** Mask pattern: 9 = digit, a = letter, * = any. e.g. '(999) 999-9999' */
    protected ?string $mask = null;

    /** Named presets: 'phone', 'creditCard', 'date', 'time', 'ssn', 'zip' */
    protected ?string $preset = null;

    public function mask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function phone(): static
    {
        $this->preset = 'phone';

        return $this;
    }

    public function creditCard(): static
    {
        $this->preset = 'creditCard';

        return $this;
    }

    public function ssn(): static
    {
        $this->preset = 'ssn';

        return $this;
    }

    public function zipCode(): static
    {
        $this->preset = 'zip';

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'mask' => $this->mask,
            'preset' => $this->preset,
        ];
    }
}
