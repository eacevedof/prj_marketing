<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class PromotionCapActionType
{
    public const VIEWED = 0;
    public const SUBSCRIBED = 1;
    public const CONFIRMED = 2;
    public const EXECUTED = 3;
    public const CANCELLED = 4;
    public const FINISHED = 5;

    public static function getAllPromotionCapTypes(): array
    {
        return [
            self::VIEWED,
            self::SUBSCRIBED,
            self::CONFIRMED,
            self::EXECUTED,
            self::CANCELLED,
            self::FINISHED,
        ];
    }
}
