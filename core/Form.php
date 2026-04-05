<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

class Form implements JsonSerializable
{
    use HasSchema;

    /** @var array<int, mixed> */
    protected array $schema = [];

    /**
     * @param  array<int, mixed>  $components
     */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSchema(string $operation = 'create'): array
    {
        $filteredSchema = array_values(array_filter(
            $this->schema,
            function (mixed $component) use ($operation): bool {
                if (method_exists($component, 'isHidden')) {
                    return ! $component->isHidden($operation);
                }

                return true;
            },
        ));

        return [
            'type' => 'form',
            'operation' => $operation,
            'schema' => array_map(
                fn (mixed $component) => static::serializeComponent($component, $operation),
                $filteredSchema,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->toSchema();
    }

    /**
     * Serialize a component, filtering nested children by operation visibility.
     *
     * @return array<string, mixed>
     */
    protected static function serializeComponent(mixed $component, string $operation): array
    {
        if (method_exists($component, 'getSchema')) {
            $children = $component->getSchema();
            $filtered = array_values(array_filter(
                $children,
                fn (mixed $child) => ! method_exists($child, 'isHidden') || ! $child->isHidden($operation),
            ));

            $data = $component->toArray();
            $data['schema'] = array_map(
                fn (mixed $child) => static::serializeComponent($child, $operation),
                $filtered,
            );

            return $data;
        }

        return $component->toArray();
    }
}
