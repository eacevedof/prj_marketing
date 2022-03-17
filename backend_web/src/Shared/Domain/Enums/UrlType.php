<?php

namespace App\Shared\Domain\Enums;

abstract class UrlType
{
    const LOGIN_FORM = "/login";
    const RESTRICT = "/restrict";
    const ERROR_NOTFOUND = "/error/not-found-404";
    const ERROR_FORBIDDEN = "/error/forbidden-403";
    const ERROR_INTERNAL = "/error/unexpected-500";
}
