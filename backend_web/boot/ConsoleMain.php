<?php
namespace Boot;

include_once "../boot/appbootstrap.php";
include_once "../vendor/autoload.php";
include_once "../vendor/theframework/bootstrap.php";

use \ReflectionClass;
use \ReflectionMethod;
use \Throwable;
use \Exception;


final class ConsoleMain
{
    private array $argv;

    public function __construct(?array $argv)
    {
        if (!(($sapi = php_sapi_name()) == "cli"))
            throw new Exception("sapi name not cli. {$sapi}");
        $this->argv = $argv ?? [];
    }

    public function exec(): void
    {
        $ar_arg = get_console_args($this->argv);
        if(isset($ar_arg["class"])) {
            $classname = $ar_arg["class"];
            $classname = str_replace(".","\\",$classname);
            $instance = new $classname();

            if(!$ar_arg["method"]) $ar_arg["method"] = "run";
            $method = $ar_arg["method"];
            $oRflecMethod = new ReflectionMethod($classname, $method);

            $arMethArgs = [];
            foreach($oRflecMethod->getParameters() as $oParam)
            {
                if(isset($ar_arg[$oParam->getName()]))
                    $arMethArgs[] =  $ar_arg[$oParam->getName()];
                else
                    $arMethArgs[] =  $oParam->getDefaultValue();
            }
            $oRflecMethod->invokeArgs($instance, $arMethArgs);
        }

        $alias = trim($this->argv[1] ?? "");
        if (!$alias) $alias = "help";

        $commands = include_once("./services.php");
        $classname = $commands[$alias] ?? "";
        if (!$classname)
            throw new Exception("no class found for cmd $alias");


        $reflection = new ReflectionClass($classname);
        //a partir de la pos 1 en adelante son parametros de input
        $input = array_slice($this->argv, 2);
        $object = $reflection->newInstanceArgs([$input]);
        if($object) $object->run();
    }

    public static function on_error(Throwable $ex): void
    {
        $uuid = uniqid();
        if ($_POST) lgerr($_POST,"console-exception $uuid POST", "error");
        if ($_GET) lgerr($_GET,"console-exception $uuid GET", "error");
        if ($_REQUEST) lgerr($_REQUEST,"console-exception $uuid REQUEST", "error");
        if ($_ENV) lgerr($_ENV,"console-exception $uuid ENV", "error");
        lgerr($ex->getMessage(), "console-exception $uuid", "error");
        lgerr($ex->getFile()." : (line: {$ex->getLine()})", "file-line $uuid", "error");
    }
}