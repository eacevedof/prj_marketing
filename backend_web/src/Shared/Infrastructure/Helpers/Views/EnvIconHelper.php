<?php
namespace App\Shared\Infrastructure\Helpers\Views;

use \ENV;

final class EnvIconHelper
{
    private const ENV_LOGO = [
        ENV::LOCAL => "/themes/mypromos/images/mypromos-logo-local.svg",
        ENV::DEV => "/themes/mypromos/images/mypromos-logo-dev.svg",
        ENV::TEST => "/themes/mypromos/images/mypromos-logo-test.svg",
        ENV::PROD => "/themes/mypromos/images/mypromos-logo-orange.svg",
    ];

    private const ENV_LOGO_RESTRICT = [
        ENV::LOCAL => "/favicon/favicon-logo-local.svg",
        ENV::DEV => "/favicon/favicon-logo-dev.svg",
        ENV::TEST => "/favicon/favicon-logo-test.svg",
        ENV::PROD => "/favicon/favicon-logo-orange.svg",
    ];

    public static function icon(): string
    {
        $env = (string) ENV::env();
        return self::ENV_LOGO[$env] ?? "";
    }

    public static function icon_restrict(): string
    {
        $env = (string) ENV::env();
        return self::ENV_LOGO_RESTRICT[$env] ?? "";
    }
}
