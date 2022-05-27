<?php

namespace App\Open\PromotionCaps\Domain\Enums;

abstract class PromotionCapUserType
{
    public const INPUT_EMAIL = "email";
    public const INPUT_NAME1 = "name1";
    public const INPUT_NAME2 = "name2";
    public const INPUT_LANGUAGE = "language";
    public const INPUT_COUNTRY = "country";
    public const INPUT_PHONE1 = "phone1";
    public const INPUT_BIRTHDATE = "birthdate";
    public const INPUT_GENDER = "gender";
    public const INPUT_ADDRESS = "address";
    public const INPUT_IS_MAILING = "is_mailing";
    public const INPUT_IS_TERMS = "is_terms";

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
            self::INPUT_IS_MAILING,
            self::INPUT_IS_TERMS,
        ];
    }
}
