<?php

namespace App\Enums;

enum Actions: string {
    case DASHBOARD_READ = "dashboard:read";
    case DASHBOARD_WRITE = "dashboard:write";
}
