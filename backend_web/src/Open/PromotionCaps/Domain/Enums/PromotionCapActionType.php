<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class PromotionCapActionType
{
    const PROMOTIONCAP_INSERT = "promotioncap.insert";

    public static function get_all(): array
    {
        return [
            self::PROMOTIONCAP_INSERT,
        ];
    }
}
