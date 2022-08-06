<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\LogsController 
 * @file LogsController.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Restrict\Logs\Infrastructure\Controllers;
use App\Shared\Infrastructure\Components\Kafka\ProducerComponent;
use App\Controllers\Open\OpenController;

final class LogsController extends OpenController
{
    public function index()
    {
        $type = $this->request->get_get("type") ?? "debug";

        $pathlogs = BOOT::PATH_LOGS."/$type/";
        if(!is_dir($pathlogs))
            return pr("No folder $pathlogs");
        
        $files = scandir($pathlogs);
        //bug($files);
        unset($files[0]); unset($files[1]);
        
        if(!$files) return pr("No log files in $pathlogs");
        
        foreach($files as $logfile)
        {
            $sPathFile = $pathlogs.$logfile;
            $sContent = file_get_contents($sPathFile);
            pr($sContent, $logfile);
            
            if($this->request->get_get("delete")) unlink($sPathFile);
        }
    }
}//LogsController
