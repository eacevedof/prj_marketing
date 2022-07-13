<?php
namespace App\Open\CookiesPolicy\Application;

use App\Shared\Infrastructure\Services\AppService;

final class CookiesPolicyInfoService extends AppService
{
    public function __invoke(): array
    {
        return [];
    }
}