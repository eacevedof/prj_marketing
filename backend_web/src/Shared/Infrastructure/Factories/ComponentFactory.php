<?php

namespace App\Shared\Infrastructure\Factories;

use App\Shared\Infrastructure\Components\Datatable\DatatableComponent;

final class ComponentFactory
{
    public static function getInstanceOf(string $component): ?object
    {
        return new $component;
    }

    public static function getDatatableComponent(array $input): ?DatatableComponent
    {
        return new DatatableComponent($input);
    }
}
