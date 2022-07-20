<?php
namespace App\Shared\Infrastructure\Helpers;

final class UrlDomainHelper extends AppHelper implements IHelper
{
    private string $env;
    private string $domain;

    public function __construct()
    {
        $this->env = getenv("APP_ENV");
        $this->domain = getenv("APP_DOMAIN");
    }

    public static function get_instance(): self
    {
        return new self();
    }

    public function get_full_url(): string
    {
        switch ($this->env) {
            case "local": return "http://$this->domain";
            default:
                return "https://$this->domain";
        }
    }
}
