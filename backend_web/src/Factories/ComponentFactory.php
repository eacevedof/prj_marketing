<?php

namespace App\Factories;

use App\Components\Datatable\DatatableComponent;

final class ComponentFactory
{
    public static function get(string $component): ?object
    {
        $component = str_replace("/","\\",$component);
        if (!strstr($component,"Component")) $component .= "Component";
        $Component = "\App\Components\\".$component;
        try {
            $obj = new $Component();
        }
        catch (\Exception $e) {
            return null;
        }
        return $obj;
    }

    public static function get_datatable(array $input): ?DatatableComponent
    {
        return new DatatableComponent($input);
    }
}