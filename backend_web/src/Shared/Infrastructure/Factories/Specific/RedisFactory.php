<?php

namespace App\Shared\Infrastructure\Factories\Specific;

use App\Shared\Infrastructure\Components\Redis\RedisComponent;

final class RedisFactory
{
    public static function getInstance(): RedisComponent
    {
        return new RedisComponent;
    }
}
