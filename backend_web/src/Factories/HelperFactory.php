<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\EncryptFactory 
 * @file EncryptFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */
namespace App\Factories;

use App\Helpers\IHelper;

final class HelperFactory
{
    public static function get(string $helper): ?IHelper
    {
        $helper = str_replace("/","\\",$helper);
        if (!strstr($helper,"Helper")) $helper .= "Helper";
        $Helper = "\App\Helpers\\".$helper;
        try {
            $obj = new $Helper();
        }
        catch (\Exception $e) {
            return null;
        }
        return $obj;
    }
}//HelperFactory
