<?php
namespace Boot;

include_once "../boot/appbootstrap.php";
include_once "../vendor/autoload.php";
include_once "../vendor/theframework/bootstrap.php";

use TheFramework\Components\ComponentRouter;
use \Throwable;

final class IndexMain
{
    private $routes = [];

    public function __construct()
    {
        $this->routes = include_once "../src/routes/routes.php";
        $this->_load_cors_headers();
    }

    private function _load_cors_headers(): void
    {
        if (isset($_SERVER["HTTP_ORIGIN"])) {
            //No "Access-Control-Allow-Origin" header is present on the requested resource.
            //should do a check here to match $_SERVER["HTTP_ORIGIN"] to a
            //whitelist of safe domains
            header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Max-Age: 86400");    // cache for 1 day
            //header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
        }
// Access-Control headers are received during OPTIONS requests
        if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
            if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

            if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
                header("Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");
        }
//si se está en producción se desactivan los mensajes en el navegador
        if (($_ENV["APP_ENV"] ?? "")=="prod") {
            $today = date("Ymd");
            ini_set("display_errors",0);
            ini_set("log_errors",1);
            //Define where do you want the log to go, syslog or a file of your liking with
            ini_set("error_log",PATH_LOGS.DS."error/sys_$today.log");
        }
    }

    private function _get_language(): string
    {
        return trim($_GET["lang"] ?? "")
                ?: trim($_COOKIE["lang"] ?? "")
                ?: trim($_SESSION["lang"] ?? "")
                ?: trim($_ENV["lang"] ?? "")
                ?: "en";
    }

    public function exec(): void
    {
        $router = new ComponentRouter($this->routes);
        $arrundata = $router->get_rundata();
        $this->routes = []; unset($router);
        
        if($methods = $arrundata["allowed"] ?? []) {
            if(!in_array($method = strtolower($_SERVER["REQUEST_METHOD"]), $methods))
                throw new \Exception("request method {$method} not allowed");
        }

        if(!$_POST && $json = file_get_contents("php://input")) 
            $_POST = json_decode($json, 1);

        $_REQUEST["ACTION"] = $arrundata;
        $_REQUEST["ACTION_LANG"] = $this->_get_language();

        $oController = new $arrundata["controller"]();
        $oController->{$arrundata["method"]}(
            ...($arrundata["_args"] ?? [])
        );        
    }

    public static function on_error(Throwable $ex): void
    {
        if ($_POST) lgerr($_POST,"index-exception POST", "error");
        if ($_GET) lgerr($_GET,"index-exception GET", "error");
        if ($_SESSION) lgerr($_SESSION,"index-exception SESSION", "error");
        if ($_REQUEST) lgerr($_REQUEST,"index-exception REQUEST", "error");
        if ($_ENV) lgerr($_ENV,"index-exception ENV", "error");
        lgerr($ex->getMessage(), "index-exception", "error");
        lgerr($ex->getFile()." : (line: {$ex->getLine()})", "", "error");

        $code = $ex->getCode()!==0 ? $ex->getCode(): 500;
        http_response_code($code);
        $response = [
            "code" => $code,
            "status" => false,
            "errors" => [
                "Unexpected error occured"
            ],
            "data" => []
        ];

        echo json_encode($response);
    }
}