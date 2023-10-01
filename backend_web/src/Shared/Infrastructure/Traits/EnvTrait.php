<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\EnvTrait
 * @file EnvTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

trait EnvTrait
{
    protected function getEnvValue(?string $key = null): string|array
    {
        return ($key === null) ? $_ENV : $_ENV[$key] ?? "";
    }

    protected function getAppEnvValue($key): string
    {
        return $this->getEnvValue("APP_{$key}");
    }

    protected function isEnvProd(): bool
    {
        return $this->getAppEnvValue("ENV") === "prod";
    }

    protected function isEnvTest(): bool
    {
        return $this->getAppEnvValue("ENV") === "test";
    }

    protected function isEnvDev(): bool
    {
        return $this->getAppEnvValue("ENV") === "dev";
    }

    protected function isEnvLocal(): bool
    {
        return $this->getAppEnvValue("ENV") === "local";
    }

    protected function addToEnv(string $key, mixed $mxValue): void
    {
        $_ENV[$key] = $mxValue;
    }

}//EnvTrait
