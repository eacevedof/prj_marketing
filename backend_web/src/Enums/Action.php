<?php

namespace App\Enums;

abstract class Action
{
    const DASHBOARD_READ = "dashboard:read";
    const DASHBOARD_WRITE = "dashboard:write";
    const PROMOTIONS_READ = "promotions:read";
    const PROMOTIONS_WRITE = "promotions:write";
}
