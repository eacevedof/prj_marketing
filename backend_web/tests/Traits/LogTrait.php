<?php
namespace Tests\Traits;

use TheFramework\Components\ComponentLog;

trait LogTrait
{
    protected function log($mxVar, $title=null): void
    {
        $oLog = new ComponentLog("test",PATH_LOGS);
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
