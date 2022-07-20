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
        return match ($this->env) {
            "local" => "http://$this->domain",
            default => "https://$this->domain",
        };
    }

    public function get_full_with_extra(string $append): string
    {
        $url = $this->get_full_url();
        return "$url/$append";
    }
}
