<?php

namespace InertiaStudio;

class Layout
{
    public static function section(string $heading): Layouts\Section
    {
        return new Layouts\Section($heading);
    }

    /**
     * @param  array<int, Tab>  $tabs
     */
    public static function tabs(array $tabs): Layouts\TabsLayout
    {
        return new Layouts\TabsLayout($tabs);
    }
}
