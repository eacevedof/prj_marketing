<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class PromotionCapUserType
{
    const INPUT_EMAIL = "email";
    const INPUT_NAME1 = "name1";
    const INPUT_NAME2 = "name2";
    const INPUT_LANGUAGE = "language";
    const INPUT_COUNTRY = "country";
    const INPUT_PHONE1 = "phone1";
    const INPUT_BIRTHDATE = "birthdate";
    const INPUT_GENDER = "gender";
    const INPUT_ADDRESS = "address";

    public static function get_all(): array
    {
        return [
            self::INPUT_EMAIL,
            self::INPUT_NAME1,
            self::INPUT_NAME2,
            self::INPUT_LANGUAGE,
            self::INPUT_COUNTRY,
            self::INPUT_PHONE1,
            self::INPUT_BIRTHDATE,
            self::INPUT_GENDER,
            self::INPUT_ADDRESS,
        ];
    }
}
