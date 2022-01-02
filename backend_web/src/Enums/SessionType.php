<?php

namespace App\Enums;

abstract class SessionType
{
    const LANG = "lang";

    const AUTH_USER = "auth_user";
    const AUTH_USER_PERMISSIONS = "auth_user_permissions";

    const PAGE_TITLE = "pagetitle";
    const KEY_CSRF = "csrf";
}
