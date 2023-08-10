<?php

namespace App\Restrict\Users\Domain\Enums;

abstract class UserPolicyType
{
    //modulos
    public const MODULE_USERS = "users";
    public const MODULE_USER_PERMISSIONS = "userpermissions";
    public const MODULE_USER_PREFERENCES = "userpreferences";
    public const MODULE_BUSINESSDATA = "businessdata";
    public const MODULE_PROMOTIONS = "promotions";
    public const MODULE_PROMOTIONS_UI = "promotionsui";
    public const MODULE_PROMOTIONS_BILLING = "promotionsbilling";
    public const MODULE_SUBSCRIPTIONS = "subscriptions";

    //politicas
    public const DASHBOARD_READ = "dashboard:read";

    public const USERS_READ = "users:read";
    public const USERS_WRITE = "users:write";

    public const USER_PERMISSIONS_READ = "userpermissions:read";
    public const USER_PERMISSIONS_WRITE = "userpermissions:write";

    public const USER_PREFERENCES_READ = "userpreferences:read";
    public const USER_PREFERENCES_WRITE = "userpreferences:write";

    public const BUSINESSDATA_READ = "businessdata:read";
    public const BUSINESSDATA_WRITE = "businessdata:write";

    //promociones
    public const PROMOTIONS_READ = "promotions:read";
    public const PROMOTIONS_WRITE = "promotions:write";

    public const PROMOTIONS_UI_READ = "promotionsui:read";
    public const PROMOTIONS_UI_WRITE = "promotionsui:write";

    public const PROMOTION_STATS_READ = "promotionstats:read";

    //subscriptions
    public const SUBSCRIPTIONS_READ = "subscriptions:read";
    public const SUBSCRIPTIONS_WRITE = "subscriptions:write";

    //billing
    public const BILLINGS_READ = "billings:read";

    public const WRITE = "write";
    public const READ = "read";

    public static function getAllPolicies(): array
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
            self::PROMOTION_STATS_READ,
            self::PROMOTIONS_UI_WRITE,
            self::SUBSCRIPTIONS_READ,
            self::SUBSCRIPTIONS_WRITE,
            self::BILLINGS_READ
        ];
    }
}
