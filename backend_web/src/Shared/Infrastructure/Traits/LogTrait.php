<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\LogTrait
 * @file LogTrait.php 1.0.0
 * @date 01-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Traits;

use TheFramework\Components\ComponentLog;

trait LogTrait
{
    protected function log($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("sql",PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logreq($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("request",PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logd($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("debug",PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logerr($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("error",PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logkafka($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("kafka",PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logpr($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("debug",PATH_LOGS);
        $mxVar = print_r($mxVar, 1);
        $oLog->save($mxVar, $title);
        echo date("Y-m-d H:i:s");
        if($title) echo "\n$title:";
        echo "\n$mxVar\n\n";
    }
    
}//LogTrait
