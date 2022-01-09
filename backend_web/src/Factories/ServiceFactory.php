<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\ServiceFactory
 * @file EncryptFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */
namespace App\Factories;

use App\Services\AppService;
use App\Services\Auth\AuthService;

final class ServiceFactory
{
    public static function get_auth(): AuthService
    {
        return AuthService::getme();
    }

    public static function get(string $service, array $params = []): ?AppService
    {
        $service = str_replace("/","\\",$service);
        if (!strstr($service,"Service")) $service .= "Service";
        
        $Service = "\App\Services\\".$service;
        return new $Service($params);
    }

    public static function get_callable(string $service, array $params = []): callable
    {
        $service = str_replace("/","\\",$service);
        if (!strstr($service,"Service")) $service .= "Service";

        $Service = "\App\Services\\".$service;
        $callable = new $Service($params);

        return $callable;
    }
}//ServiceFactory
