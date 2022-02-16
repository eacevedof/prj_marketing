<?php

namespace App\Restrict\Users\Domain\Enums;

abstract class UserProfileType
{
    const ROOT = "1";
    const SYS_ADMIN = "2";
    const BUSINESS_OWNER = "3";
    const BUSINESS_MANAGER = "4";

    const ROOT_SUPER_UUID = "sys000001";
    const ROOT_SUPER_ID = 1;
}
