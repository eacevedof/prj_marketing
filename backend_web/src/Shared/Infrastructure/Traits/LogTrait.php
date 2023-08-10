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

use BOOT;
use TheFramework\Components\ComponentLog;

trait LogTrait
{
    protected function logSql(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("sql", BOOT::PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logRequest(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("request", BOOT::PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logDebug(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("debug", BOOT::PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logErr(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("error", BOOT::PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logKafka(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("kafka", BOOT::PATH_LOGS);
        $oLog->save($mxVar, $title);
    }

    protected function logPr(mixed $mxVar, $title = null): void
    {
        $oLog = new ComponentLog("debug", BOOT::PATH_LOGS);
        $mxVar = print_r($mxVar, 1);
        $oLog->save($mxVar, $title);
        echo date("Y-m-d H:i:s");
        if ($title) {
            echo "\n$title:";
        }
        echo "\n$mxVar\n\n";
    }

}//LogTrait
