<?php

namespace InertiaStudio;

class Filter
{
    public static function select(string $name): Filters\SelectFilter
    {
        return new Filters\SelectFilter($name);
    }

    public static function ternary(string $name): Filters\TernaryFilter
    {
        return new Filters\TernaryFilter($name);
    }

    public static function date(string $name): Filters\DateFilter
    {
        return new Filters\DateFilter($name);
    }

    public static function boolean(string $name): Filters\BooleanFilter
    {
        return new Filters\BooleanFilter($name);
    }

    public static function query(string $name): Filters\QueryFilter
    {
        return new Filters\QueryFilter($name);
    }
}
