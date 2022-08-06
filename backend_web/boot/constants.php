<?php
//constants.php 20200721
define("PATH_ROOT", dirname(__DIR__));

abstract class BOOT
{
    public const PATH_ROOT = PATH_ROOT;
    public const PATH_PUBLIC = PATH_ROOT."/public";
    public const PATH_VENDOR = PATH_ROOT."/vendor";
    public const PATH_SRC = PATH_ROOT."/src";
    public const PATH_DISK_CACHE = PATH_ROOT."/cache";
    public const PATH_SRC_CONFIG = PATH_ROOT."/config";
    public const PATH_LOGS = PATH_ROOT."/logs";
    public const PATH_CONSOLE = PATH_ROOT."/console";
}

abstract class ENV
{
    public const LOCAL = "local";
    public const DEV = "dev";
    public const TEST = "test";
    public const PROD = "prod";

    public static function is_local(): bool { self::LOCAL === getenv("APP_ENV");}
    public static function is_dev(): bool { self::DEV === getenv("APP_ENV");}
    public static function is_test(): bool { self::TEST === getenv("APP_ENV");}
    public static function is_prod(): bool { self::PROD === getenv("APP_ENV");}
}