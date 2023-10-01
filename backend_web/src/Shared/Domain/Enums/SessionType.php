<?php

namespace App\Shared\Domain\Enums;

abstract class SessionType
{
    public const AUTH_USER_LANG = "lang";
    public const AUTH_USER_TZ = "tz";
    public const AUTH_USER_ID_TZ = "id_tz";

    public const AUTH_USER = "auth_user";
    public const AUTH_USER_PERMISSIONS = "auth_user_permissions";
}
