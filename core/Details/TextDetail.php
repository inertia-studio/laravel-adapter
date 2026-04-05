<?php

namespace InertiaStudio\Details;

class TextDetail extends BaseDetail
{
    protected string $type = 'text';

    protected ?string $formatAs = null;

    /** @var array<string, mixed> */
    protected array $formatOptions = [];

    protected ?int $limit = null;

    protected ?string $weight = null;

    public function dateTime(?string $format = null): static
    {
        $this->formatAs = 'dateTime';

        if ($format !== null) {
            $this->formatOptions['format'] = $format;
        }

        return $this;
    }

    public function money(?string $currency = null, ?string $locale = null): static
    {
        $this->formatAs = 'money';

        if ($currency !== null) {
            $this->formatOptions['currency'] = $currency;
        }

        if ($locale !== null) {
            $this->formatOptions['locale'] = $locale;
        }

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function weight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'formatAs' => $this->formatAs,
            'formatOptions' => $this->formatOptions,
            'limit' => $this->limit,
            'weight' => $this->weight,
        ];
    }
}
