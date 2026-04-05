<?php

namespace InertiaStudio;

class Detail
{
    public static function text(string $name): Details\TextDetail
    {
        return new Details\TextDetail($name);
    }

    public static function badge(string $name): Details\BadgeDetail
    {
        return new Details\BadgeDetail($name);
    }

    public static function boolean(string $name): Details\BooleanDetail
    {
        return new Details\BooleanDetail($name);
    }

    public static function image(string $name): Details\ImageDetail
    {
        return new Details\ImageDetail($name);
    }

    public static function date(string $name): Details\DateDetail
    {
        return new Details\DateDetail($name);
    }

    public static function money(string $name): Details\MoneyDetail
    {
        return new Details\MoneyDetail($name);
    }
}
