<?php

namespace InertiaStudio\Fields;

class OtpField extends BaseField
{
    protected string $type = 'otp';

    protected int $length = 6;

    protected bool $numericOnly = true;

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function alphanumeric(): static
    {
        $this->numericOnly = false;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'length' => $this->length,
            'numericOnly' => $this->numericOnly,
        ];
    }
}
