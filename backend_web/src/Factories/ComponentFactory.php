<?php

namespace App\Factories;

use App\Repositories\AppRepository;

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
}