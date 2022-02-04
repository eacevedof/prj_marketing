<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ConsoleTrait
 * @file ConsoleTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Traits;

trait ConsoleTrait
{
    protected $input;

    private function _pr($mxVar, string $title=""):void
    {
        $now = date("Y-m-d H:i:s");
        $message = "\n$now\n";
        if($title) $message .= $title ."\n";
        $message .= is_string($mxVar)? $mxVar: var_export($mxVar,1);
        $message .= "\n";
        echo $message;
    }
}//ConsoleTrait
