<?php

namespace App\Enums;

abstract class PolicyType
{
    const USERS_READ = "users:read";
    const USERS_WRITE = "users:write";

    const DASHBOARD_READ = "dashboard:read";
    const DASHBOARD_WRITE = "dashboard:write";

    const PROMOTIONS_READ = "promotions:read";
    const PROMOTIONS_WRITE = "promotions:write";
}
