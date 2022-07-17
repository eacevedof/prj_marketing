<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class RequestActionType
{
    public const PROMOTIONCAP_INSERT = "promotioncap.insert";
    public const HOME_CONTACT_SEND = "home.contact.send";

    public static function get_all(): array
    {
        return [
            self::PROMOTIONCAP_INSERT,
            self::HOME_CONTACT_SEND,
        ];
    }
}
