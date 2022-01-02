<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\LogsController 
 * @file LogsController.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Controllers;
use App\Components\Kafka\ProducerComponent;
use App\Controllers\Open\OpenController;

final class LogsController extends OpenController
{
    public function index()
    {
        $type = $this->request->get_get("type") ?? "debug";

        $pathlogs = PATH_LOGS.DS.$type.DS;
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
