<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\AppController
 * @file AppController.php v1.2.0
 * @date 01-07-2021 20:14 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Controllers;

use App\Shared\Infrastructure\Traits\{EnvTrait, ErrorTrait, LogTrait};

abstract class AppController
{
    use EnvTrait;
    use ErrorTrait;
    use LogTrait;

    protected function _request_log(): void
    {
        $sReqUri = $_SERVER["REQUEST_URI"];
        $this->logRequest("appcontroller._request_log");
        $this->logRequest($_SERVER["HTTP_USER_AGENT"] ?? "", "HTTP_USER_AGENT");
        $this->logRequest($_SERVER["REMOTE_ADDR"] ?? "", "REMOTE_ADDR");
        $this->logRequest($_SERVER["REMOTE_HOST"] ?? "", "REMOTE_HOST");
        $this->logRequest($_SERVER["HTTP_HOST"] ?? "", "HTTP_HOST");
        //$this->logd($_SERVER["REMOTE_USER"] ?? "","REMOTE_USER");

        $this->logRequest($_FILES, "$sReqUri FILES");
        $this->logRequest($_SESSION, "$sReqUri SESSION");
        $this->logRequest($_GET, "$sReqUri GET");
        $this->logRequest($_POST, "$sReqUri POST");
    }

}//AppController
