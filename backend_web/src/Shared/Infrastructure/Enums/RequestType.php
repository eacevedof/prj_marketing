<?php

namespace App\Shared\Infrastructure\Enums;

abstract class RequestType
{
    const LANG = "lang";
    const CSRF = "_csrf";
    const ACTION = "_action";
}