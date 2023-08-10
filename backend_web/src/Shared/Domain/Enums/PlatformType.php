<?php

namespace App\Shared\Domain\Enums;

abstract class PlatformType
{
    public const ETL = "0";
    public const MOBILE = "1";
    public const WEB = "2";
    public const CONSOLE = "3";
}
