<?php

namespace App\Restrict\Users\Domain\Enums;

abstract class UserProfileType
{
    public const ROOT = 1;
    public const SYS_ADMIN = 2;
    public const BUSINESS_OWNER = 3;
    public const BUSINESS_MANAGER = 4;

    const ROOT_SUPER_UUID = "sys000001";
    const ROOT_SUPER_ID = 1;
}
