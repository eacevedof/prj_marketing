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

use App\Models\AppEntity;

final class EntityFactory
{
    public static function get(string $entity): ?AppEntity
    {
        $entity = str_replace("/","\\",$entity);
        if (!strstr($entity,"Model")) $entity .= "Model";
        $Model = "\App\Models\\".$entity;
        try {
            $obj = new $Model();
        }
        catch (\Exception $e) {
            return null;
        }
        return $obj;
    }
}//ServiceFactory
