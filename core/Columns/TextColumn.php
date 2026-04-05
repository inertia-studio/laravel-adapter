<?php

namespace InertiaStudio\Columns;

class TextColumn extends BaseColumn
{
    protected string $type = 'text';

    protected ?int $limit = null;

    protected ?int $words = null;

    protected ?string $weight = null;

    protected ?string $size = null;

    protected ?string $color = null;

    protected ?string $formatAs = null;

    /** @var array<string, mixed> */
    protected array $formatOptions = [];

    public function dateTime(): static
    {
        $this->formatAs = 'dateTime';

        return $this;
    }

    public function money(): static
    {
        $this->formatAs = 'money';

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function words(int $words): static
    {
        $this->words = $words;

        return $this;
    }

    public function weight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'limit' => $this->limit,
            'words' => $this->words,
            'weight' => $this->weight,
            'size' => $this->size,
            'color' => $this->color,
            'formatAs' => $this->formatAs,
            'formatOptions' => $this->formatOptions,
        ];
    }
}
