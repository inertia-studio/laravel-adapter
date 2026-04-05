<?php

namespace InertiaStudio\Columns;

class MoneyColumn extends BaseColumn
{
    protected string $type = 'money';

    protected ?string $currency = null;

    protected ?string $locale = null;

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'currency' => $this->currency,
            'locale' => $this->locale,
        ];
    }
}
