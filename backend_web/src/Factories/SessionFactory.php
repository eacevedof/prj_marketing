<?php


namespace App\Factories;

use App\Components\Session\SessionComponent;

final class SessionFactory
{
    public static function get(): SessionComponent
    {
        return new SessionComponent();
    }
}