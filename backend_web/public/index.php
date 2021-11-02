<?php
ob_start();
session_name("MARKETINGID");
session_start();
include("../boot/appbootstrap.php");

//header("Access-Control-Allow-Origin: *");
//Código de configuración de cabeceras que permiten consumir la API desde cualquier origen
//fuente: https://stackoverflow.com/questions/14467673/enable-cors-in-htaccess
// Allow from any origin
if(isset($_SERVER["HTTP_ORIGIN"]))
{
    //No "Access-Control-Allow-Origin" header is present on the requested resource.
    //should do a check here to match $_SERVER["HTTP_ORIGIN"] to a
    //whitelist of safe domains
    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");    // cache for 1 day
    //header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");
}

// Access-Control headers are received during OPTIONS requests
if($_SERVER["REQUEST_METHOD"] == "OPTIONS")
{
    if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if(isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
        header("Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");
}
//si se está en producción se desactivan los mensajes en el navegador
if($_ENV["APP_ENV"]=="prod")
{
    $sToday = date("Ymd");
    ini_set("display_errors",0);
    ini_set("log_errors",1);
    //Define where do you want the log to go, syslog or a file of your liking with
    ini_set("error_log",PATH_LOGS.DS."sys_$sToday.log"); // or ini_set("error_log", "/path/to/syslog/file")
}

//autoload de composer
include_once "../vendor/autoload.php";
//arranque de mis utilidades
include_once "../vendor/theframework/bootstrap.php";
//rutas, mapeo de url => controlador.metodo()
$arRoutes = include_once "../src/routes/routes.php";

use TheFramework\Components\ComponentRouter;
try {
    $oR = new ComponentRouter($arRoutes);
    $arRun = $oR->get_rundata();
    unset($arRoutes);

    if($methods = $arRun["allowed"] ?? []) {
        if(!in_array($method = strtolower($_SERVER["REQUEST_METHOD"]), $methods))
            throw new \Exception("request method {$_SERVER["REQUEST_METHOD"]} not allowed");
    }

    if($json = file_get_contents("php://input")) $_POST = json_decode($json, 1);

    $_REQUEST["ACTION"] = $arRun;
    $_REQUEST["ACTION_LANG"] = trim($_GET["lang"] ?? "")
                                ?: trim($_COOKIE["lang"] ?? "")
                                ?: trim($_SESSION["lang"] ?? "")
                                ?: trim($_ENV["lang"] ?? "")
                                ?: "en";

    $oController = new $arRun["controller"]();
    $oController->{$arRun["method"]}();

}
catch (\Exception $ex)
{
    lg($ex->getMessage(), "main-exception", "error");
    lg($_POST,"main-exception POST", "error");
    lg($_GET,"main-exception GET", "error");
    lg($_SESSION,"main-exception SESSION", "error");
    lg($_REQUEST,"main-exception REQUEST", "error");
    lg($_ENV,"main-exception ENV", "error");
    http_response_code(500);
    $response = [
        "status" => false,
        "errors" => [
            $ex->getMessage()
        ],
        "data" => []
    ];
    echo json_encode($response);
}

catch (\Throwable $ex)
{
    lg($ex->getMessage(), "fatal-error", "error");
    lg($_POST,"fatal-error POST", "error");
    lg($_GET,"fatal-error GET", "error");
    lg($_SESSION,"fatal-error SESSION", "error");
    lg($_REQUEST,"fatal-error REQUEST", "error");
    lg($_ENV,"fatal-error ENV", "error");

    http_response_code(500);
    $response = [
        "status" => false,
        "errors" => [
            "Server throwable error"
        ],
        "data" => []
    ];
    echo json_encode($response);
}
ob_end_flush();
