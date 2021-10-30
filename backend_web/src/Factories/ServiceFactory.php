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


final class ServiceFactory
{
    public static function get(string $service, array $params = []): ?object
    {
        $service = "App\Sevices\{$service}";
        try {
            $reflection = new $service(...$params);
        }
        catch (\Exception $e) {
            return null;
        }
        return $reflection;
    }
}//ServiceFactory
