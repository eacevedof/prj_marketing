<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class RequestActionType
{
    const PROMOTIONCAP_INSERT = "promotioncap.insert";

    public static function get_all(): array
    {
        return [
            self::PROMOTIONCAP_INSERT,
        ];
    }
}
