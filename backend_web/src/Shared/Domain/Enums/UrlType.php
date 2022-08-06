<?php

namespace App\Shared\Domain\Enums;

abstract class UrlType
{
    public const LOGIN_FORM = "/login";
    public const RESTRICT = "/restrict";
    public const ERROR_NOTFOUND = "/error/not-found-404";
    public const ERROR_FORBIDDEN = "/error/forbidden-403";
    public const ERROR_INTERNAL = "/error/unexpected-500";
}
