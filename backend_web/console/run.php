<?php
//index.php 3.0.0
const DS = DIRECTORY_SEPARATOR;
const DOCROOT = __DIR__;

$sPath = realpath(DOCROOT.DS."..");
define("PATH_ROOT",$sPath);
$sPath = realpath(DOCROOT.DS."../src");
define("PATH_SRC",$sPath);
$sPath = realpath(DOCROOT.DS."../config");
define("PATH_SRC_CONFIG",$sPath);
$sPath = realpath(DOCROOT.DS."../console");
define("PATH_CONSOLE",$sPath);
$sPath = realpath(DOCROOT.DS."../public");
define("PATH_PUBLIC",$sPath);
$sPath = realpath(DOCROOT.DS."../vendor");
define("PATH_VENDOR",$sPath);
$sPath = realpath(DOCROOT.DS."../logs");
define("PATH_LOGS",$sPath);
$sPath = realpath(DOCROOT.DS."../boot");
define("PATH_BOOT",$sPath);
$pathenv = realpath(DOCROOT.DS."../.env");

include_once PATH_BOOT.DS."functions.php";
//autoload de composer
include_once "../vendor/autoload.php";
//arranque de mis utilidades
include_once "../vendor/theframework/bootstrap.php";

appboot_loadenv();
//console_loadenv($pathenv);

$env = getenv("APP_ENV");
if($env==="prod-xxx")
{
    $sToday = date("Ymd");
    ini_set("display_errors",0);
    ini_set("log_errors",1);
    ini_set("error_log",PATH_LOGS."/sys_$sToday.log");
}

$isCLI = (php_sapi_name() == "cli");
if($isCLI)
{
    $ar_arg = get_console_args($argv);

    if(isset($ar_arg["class"]))
    {
        $classname = $ar_arg["class"];
        $classname = str_replace(".","\\",$classname);
        try {
            $instance = new $classname();

            if(!$ar_arg["method"]) $ar_arg["method"] = "run";

            $method = $ar_arg["method"];
            $oRflecMethod = new \ReflectionMethod($classname, $method);

            $arMethArgs = [];
            foreach($oRflecMethod->getParameters() as $oParam)
            {
                if(isset($ar_arg[$oParam->getName()]))
                    $arMethArgs[] =  $ar_arg[$oParam->getName()];
                else
                    $arMethArgs[] =  $oParam->getDefaultValue();
            }

            return $oRflecMethod->invokeArgs($instance, $arMethArgs);
        }
        catch (\Throwable $e) {
            echo "error:\n\t{$e->getMessage()}\n\n";
        }
    }

    //parámetros separados por espacio
    $alias = trim($argv[1] ?? "");
    if (!$alias) $alias = "help";
    try {
        $commands = include_once("./services.php");
        $classname = $commands[$alias] ?? "";
        if (!$classname) {
            echo "no class found for cmd $alias";
            return;
        }
        $reflection = new \ReflectionClass($classname);
        //a partir de la pos 1 en adelante son parametros de input
        $input = array_slice($argv, 2);
        $object = $reflection->newInstanceArgs([$input]);
        if($object) return $object->run();
    }
    catch (\Throwable $e) {
        echo "error:\n\t{$e->getMessage()}\n\n";
    }

}// is cli