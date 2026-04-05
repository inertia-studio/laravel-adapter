<?php

namespace InertiaStudio;

class Column
{
    public static function text(string $name): Columns\TextColumn
    {
        return new Columns\TextColumn($name);
    }

    public static function badge(string $name): Columns\BadgeColumn
    {
        return new Columns\BadgeColumn($name);
    }

    public static function boolean(string $name): Columns\BooleanColumn
    {
        return new Columns\BooleanColumn($name);
    }

    public static function image(string $name): Columns\ImageColumn
    {
        return new Columns\ImageColumn($name);
    }

    public static function icon(string $name): Columns\IconColumn
    {
        return new Columns\IconColumn($name);
    }

    public static function color(string $name): Columns\ColorColumn
    {
        return new Columns\ColorColumn($name);
    }

    public static function date(string $name): Columns\DateColumn
    {
        return new Columns\DateColumn($name);
    }

    public static function money(string $name): Columns\MoneyColumn
    {
        return new Columns\MoneyColumn($name);
    }
}
