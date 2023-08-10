<?php

namespace App\Shared\Infrastructure\Components\Request;

final class RequestComponent
{
    public function getPost(?string $key = null, mixed $default = null): mixed
    {
        if (!$key) {
            return $_POST ?? [];
        }
        return $_POST[$key] ?? $default;
    }

    public function getGet(?string $sKey = null, mixed $default = null): mixed
    {
        if (!$sKey) {
            return $_GET ?? [];
        }
        return $_GET[$sKey] ?? $default;
    }

    public function getRequest(?string $sKey = null, mixed $default = null): mixed
    {
        if (!$sKey) {
            return $_REQUEST ?? [];
        }
        return $_REQUEST[$sKey] ?? $default;
    }

    public function getFiles(?string $sKey = null): mixed
    {
        if (!$sKey) {
            return $_FILES ?? [];
        }
        return $_FILES[$sKey] ?? null;
    }

    public function getRemoteIp(): string
    {
        if ($ip = ($_SERVER["HTTP_CLIENT_IP"] ?? "")) {
            return $ip;
        }
        if ($ip = ($_SERVER["HTTP_X_FORWARDED_FOR"] ?? "")) {
            return $ip;
        }
        if ($ip = $_SERVER["REMOTE_ADDR"]) {
            return $ip;
        }
        return "127.0.0.1";
    }

    public function getLang(): string
    {
        return $_REQUEST["lang"] ?? "en";
    }

    public function setLang(string $lang = "en"): void
    {
        $_REQUEST["lang"] = $lang;
    }

    public function isPostPayload(?string $sKey = null): bool
    {
        return $sKey ? isset($_POST[$sKey]) : count($_POST) > 0;
    }

    public function isGetPayload(?string $sKey = null): bool
    {
        return $sKey ? isset($_GET[$sKey]) : count($_GET) > 0;
    }

    public function isFilePayload(string $sKey = null): bool
    {
        return $sKey ? isset($_FILES[$sKey]) : count($_FILES) > 0;
    }

    public function getRequestMethod(): string
    {
        return strtolower($_SERVER["REQUEST_METHOD"]) ?? "";
    }

    public function isPutMethod(): bool
    {
        return $this->getRequestMethod() === "put";
    }

    public function isPatchMethod(): bool
    {
        return $this->getRequestMethod() === "patch";
    }

    public function isDeleteMethod(): bool
    {
        return $this->getRequestMethod() === "delete";
    }

    public function isPostMethod(): bool
    {
        return $this->getRequestMethod() === "post";
    }

    public function getHeaderValueByKey(?string $key = null): ?string
    {
        $all = getallheaders();
        if (!$key) {
            return $all;
        }
        foreach ($all as $k => $v) {
            if(strtolower($k) === strtolower($key)) {
                return $v;
            }
        }
        return null;
    }

    public function getReferer(): ?string
    {
        return $_SERVER["HTTP_REFERER"] ?? null;
    }

    public function getRequestUri(): ?string
    {
        return $_SERVER["REQUEST_URI"] ?? null;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->getGet("redirect");
    }

    public function doClientAcceptJson(): bool
    {
        $accept = $this->getHeaderValueByKey("accept");
        $accept = strtolower($accept);
        return strstr($accept, "application/json");
    }

}
