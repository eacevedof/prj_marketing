<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\ServiceFactory
 * @file EncryptFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Factories;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Infrastructure\Services\AppService;

final class ServiceFactory
{
    public static function getAuthService(): AuthService
    {
        return AuthService::getInstance();
    }

    public static function getInstanceOf(string $service, array $params = []): ?AppService
    {
        return new $service($params);
    }

    public static function getSimpleInstanceOf(string $service): ?AppService
    {
        return new $service;
    }

    public static function getCallableService(string $service, array $params = []): callable
    {
        $callable = new $service($params);
        return $callable;
    }
}//ServiceFactory
