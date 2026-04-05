<?php

namespace InertiaStudio\Layouts;

use InertiaStudio\Concerns\HasSchema;
use InertiaStudio\Tab;
use JsonSerializable;

class TabsLayout implements JsonSerializable
{
    use HasSchema;

    public function __construct(
        /** @var array<int, Tab> */
        protected array $tabs,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'tabs',
            'tabs' => array_map(
                fn (mixed $tab) => $tab->toArray(),
                $this->tabs,
            ),
        ];
    }
}
