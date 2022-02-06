<?php

namespace App\Shared\Domain\Enums;

abstract class RequestType
{
    const LANG = "lang";
    const CSRF = "_csrf";
    const ACTION = "_action";
}
