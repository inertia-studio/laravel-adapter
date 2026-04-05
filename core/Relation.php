<?php

namespace InertiaStudio;

use InertiaStudio\Concerns\HasSchema;
use JsonSerializable;

abstract class Relation implements JsonSerializable
{
    use HasSchema;

    protected static string $relationship = '';

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelationshipName(): string
    {
        return static::$relationship;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'relationship' => static::$relationship,
            'form' => $this->form(new Form)->toSchema(),
            'table' => $this->table(new Table)->toSchema(),
        ];
    }
}
