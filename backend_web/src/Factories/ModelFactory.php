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

use App\Models\AppModel;

final class ModelFactory
{
    public static function get(string $model): ?AppModel
    {
        $model = str_replace("/","\\",$model);
        if (!strstr($model,"Model")) $model .= "Model";
        $Model = "\App\Models\\".$model;
        try {
            $obj = new $Model();
        }
        catch (\Exception $e) {
            return null;
        }
        return $obj;
    }
}//ServiceFactory
