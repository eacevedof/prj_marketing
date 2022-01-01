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
use App\Traits\RequestTrait;

abstract class AppController
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;
    use RequestTrait;

    public function request_log(): void
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
    
    public function response_json($arData): string
    {
        header("Content-type: application/json");
        echo json_encode($arData);        
    }

}//AppController
