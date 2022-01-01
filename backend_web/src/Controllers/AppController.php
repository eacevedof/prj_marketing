<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\AppController 
 * @file AppController.php v1.2.0
 * @date 01-07-2021 20:14 SPAIN
 * @observations
 */
namespace App\Controllers;

use App\Traits\LogTrait;
use App\Traits\EnvTrait;
use App\Traits\ErrorTrait;

abstract class AppController
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    public function __construct() 
    {

    }

    /**
     * lee valores de $_POST
     */
    protected function get_post($sKey=NULL)
    {
        if(!$sKey) return $_POST ?? [];
        return (isset($_POST[$sKey]) ? $_POST[$sKey] : "");
    }

    /**
     * lee valores de $_FILES
     */
    protected function get_files($sKey=NULL)
    {
        if(!$sKey) return $_FILES ?? [];
        return (isset($_FILES[$sKey])?$_FILES[$sKey]:"");
    }
    
    protected function is_post(){return count($_POST)>0;}

    /**
     * lee valores de $_GET
     */
    protected function get_get($sKey=NULL)
    {
        if(!$sKey) return $_GET ?? [];
        return (isset($_GET[$sKey])?$_GET[$sKey]:"");
    }

    protected function get_session($sKey=NULL)
    {
        if(!$sKey) return $_SESSION ?? [];
        return (isset($_SESSION[$sKey]) ? $_SESSION[$sKey] : "");
    }
    
    protected function is_get($sKey=NULL){if($sKey) return isset($_GET[$sKey]); return count($_GET)>0;}
    
    protected function request_log(): void
    {
        $sReqUri = $_SERVER["REQUEST_URI"];
        $this->logreq($_SERVER["HTTP_USER_AGENT"] ?? "","HTTP_USER_AGENT");
        $this->logreq($_SERVER["REMOTE_ADDR"] ?? "","REMOTE_ADDR");
        $this->logreq($_SERVER["REMOTE_HOST"] ?? "","REMOTE_HOST");
        $this->logreq($_SERVER["HTTP_HOST"] ?? "","HTTP_HOST");
        //$this->logd($_SERVER["REMOTE_USER"] ?? "","REMOTE_USER");

        $this->logreq($this->get_files(),"$sReqUri FILES");
        $this->logreq($this->get_session(), "$sReqUri SESSION");
        $this->logreq($this->get_get(),"$sReqUri GET");
        $this->logreq($this->get_post(),"$sReqUri POST");
    }
    
    protected function response_json($arData)
    {
        header("Content-type: application/json");
        echo json_encode($arData);        
    }

    protected function get_header($key=null)
    {
        $all = getallheaders();
        $this->logreq($all,"get_header.all");
        if(!$key) return $all;
        foreach ($all as $k=>$v)
            if(strtolower($k)===strtolower($key))
                return $v;
        return null;
/*
 Ejemplo de all:
  "Host" => "localhost:10000",
  "Connection" => "keep-alive",
  "Content-Length" => "883",
  "Accept" => "application/json, text/plain, * /*",
  "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36",
  "Content-Type" => "multipart/form-data; boundary=----WebKitFormBoundaryvqgSyJucPdRuOBVB",
  "Origin" => "http://localhost:3000",
  "Sec-Fetch-Site" => "same-site",
  "Sec-Fetch-Mode" => "cors",
  "Sec-Fetch-Dest" => "empty",
  "Referer" => "http://localhost:3000/admin/product/516",
  "Accept-Encoding" => "gzip, deflate, br",
  "Accept-Language" => "es-ES,es;q=0.9,en;q=0.8,lt;q=0.7",
 */
    }


}//AppController
