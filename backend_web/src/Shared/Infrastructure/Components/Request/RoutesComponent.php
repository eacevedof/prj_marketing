<?php

namespace App\Shared\Infrastructure\Components\Request;

use TheFramework\Components\ComponentRouter;

final class RoutesComponent
{
    public static function url(string $name, array $args=[]): string
    {
        return ComponentRouter::get_url($name, $args);
    }
}