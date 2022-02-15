<?php

namespace App\Restrict\Users\Domain\Enums;

abstract class UserPolicyType
{
    const DASHBOARD_READ = "dashboard:read";

    const USERS_READ = "users:read";
    const USERS_WRITE = "users:write";

    const PROMOTIONS_READ = "promotions:read";
    const PROMOTIONS_WRITE = "promotions:write";

    const USER_PERMISSIONS_READ = "userpermissions:read";
    const USER_PERMISSIONS_WRITE = "userpermissions:write";
}
