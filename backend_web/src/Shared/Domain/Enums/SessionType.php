<?php

namespace App\Shared\Domain\Enums;

abstract class SessionType
{
    const LANG = "lang";
    const TZ = "tz";

    const AUTH_USER = "auth_user";
    const AUTH_USER_PERMISSIONS = "auth_user_permissions";
}
