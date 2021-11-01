<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\RepositoryFactory 
 * @file RepositoryFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */
namespace App\Factories;

use App\Repositories\AppRepository;

final class RepositoryFactory
{
    public static function get(string $repository): ?AppRepository
    {
        $repository = str_replace("/","\\",$repository);
        if(strstr($repository,"Repository")) $repository .= "Repository";
        $Repository = "\App\Repositories\\".$repository;
        try {
            $obj = new $Repository();
        }
        catch (\Exception $e) {
            return null;
        }
        return $obj;
    }
}//ServiceFactory
