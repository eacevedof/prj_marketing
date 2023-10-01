<?php

namespace App\Shared\Infrastructure\Helpers;

final class UrlDomainHelper extends AppHelper implements IHelper
{
    private string $appEnv;
    private string $appDomain;

    public function __construct()
    {
        $this->appEnv = getenv("APP_ENV");
        $this->appDomain = getenv("APP_DOMAIN");
    }

    public static function getInstance(): self
    {
        return new self;
    }

    private function _getDomainFullUrlByAppEnv(): string
    {
        return match ($this->appEnv) {
            "local" => "http://$this->appDomain",
            default => "https://$this->appDomain",
        };
    }

    public function getDomainUrlWithAppend(?string $append = null): string
    {
        $url = $this->_getDomainFullUrlByAppEnv();
        if (is_null($append)) {
            return $url;
        }
        return str_starts_with($append, "/") ? "$url$append" : "$url/$append";
    }
}
