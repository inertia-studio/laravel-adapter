<?php

namespace InertiaStudio\Fields;

class PasswordField extends BaseField
{
    protected string $type = 'password';

    protected bool $revealable = false;

    public function revealable(): static
    {
        $this->revealable = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'revealable' => $this->revealable,
        ];
    }
}
