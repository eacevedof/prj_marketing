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

    private function _on_class_method_params(array $argv): void
    {
        $classname = $argv["class"];
        $classname = str_replace(".","\\",$classname);
        $instance = new $classname();

        if(!$argv["method"]) $argv["method"] = "run";
        $method = $argv["method"];
        $oRflecMethod = new ReflectionMethod($classname, $method);

        $armethparams = [];
        foreach($oRflecMethod->getParameters() as $oParam) {
            if(isset($args[$oParam->getName()]))
                $armethparams[] =  $args[$oParam->getName()];
            else
                $armethparams[] =  $oParam->getDefaultValue();
        }
        $oRflecMethod->invokeArgs($instance, $armethparams);        
    }

    private function _on_service_arguments(): void
    {
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
    
    public function exec(): void
    {
        $args = get_console_args($this->argv);
        if(isset($args["class"])) {
            $this->_on_class_method_params($args);
            return;
        }

        $this->_on_service_arguments();
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

    public static function debug(Throwable $ex): void
    {
        if (getenv("APP_ENV")==="prod") return;
        if (!((bool) getenv("APP_DEBUG"))) return;

        $content = [];
        $content["Exception"] = $ex->getMessage();
        $content["File"] = $ex->getFile()."(".$ex->getLine().")";
        $code = $ex->getCode()!==0 ? $ex->getCode(): 500;
        $content["response"] = $code;

        if ($_POST) $content["POST"] = var_export($_POST, 1);
        if ($_GET) $content["GET"] = var_export($_GET, 1);
        if ($_SESSION) $content["SESSION"] = var_export($_SESSION, 1);
        if ($_REQUEST) $content["REQUEST"] = var_export($_REQUEST, 1);
        if ($_ENV) $content["ENV"] = var_export($_ENV, 1);

        print_r($content);
    }
}