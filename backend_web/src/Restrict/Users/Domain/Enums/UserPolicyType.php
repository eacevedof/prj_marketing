<?php

namespace App\Restrict\Users\Domain\Enums;

abstract class UserPolicyType
{
    //modulos
    const MODULE_USERS = "users";
    const MODULE_USER_PERMISSIONS = "userpermissions";
    const MODULE_USER_PREFERENCES = "userpreferences";
    const MODULE_BUSINESSDATA = "businessdata";
    const MODULE_PROMOTIONS = "promotions";
    const MODULE_PROMOTIONS_UI = "promotionsui";

    //politicas
    const DASHBOARD_READ = "dashboard:read";

    const USERS_READ = "users:read";
    const USERS_WRITE = "users:write";

    const USER_PERMISSIONS_READ = "userpermissions:read";
    const USER_PERMISSIONS_WRITE = "userpermissions:write";

    const USER_PREFERENCES_READ = "userpreferences:read";
    const USER_PREFERENCES_WRITE = "userpreferences:write";

    const BUSINESSDATA_READ = "businessdata:read";
    const BUSINESSDATA_WRITE = "businessdata:write";

    //promociones
    const PROMOTIONS_READ = "promotions:read";
    const PROMOTIONS_WRITE = "promotions:write";

    const PROMOTIONS_UI_READ = "promotionsui:read";
    const PROMOTIONS_UI_WRITE = "promotionsui:write";

    //subscriptions
    const SUBSCRIPTIONS_READ = "subscriptions:read";
    const SUBSCRIPTIONS_WRITE = "subscriptions:write";

    const WRITE = "write";
    const READ = "read";

    public static function get_all(): array
    {
        return [
            self::DASHBOARD_READ,
            self::USERS_READ,
            self::USERS_WRITE,
            self::USER_PERMISSIONS_READ,
            self::USER_PERMISSIONS_WRITE,
            self::USER_PREFERENCES_READ,
            self::USER_PREFERENCES_WRITE,
            self::BUSINESSDATA_READ,
            self::BUSINESSDATA_WRITE,
            self::PROMOTIONS_READ,
            self::PROMOTIONS_WRITE,
            self::PROMOTIONS_UI_READ,
            self::PROMOTIONS_UI_WRITE,
            self::SUBSCRIPTIONS_READ,
            self::SUBSCRIPTIONS_WRITE
        ];
    }
}
