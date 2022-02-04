<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\EncryptFactory 
 * @file EncryptFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Factories;

use App\Shared\Domain\Entities\AppEntity;

final class EntityFactory
{
    public static function get(string $entity): ?AppEntity
    {
        return new $entity();
    }
}//ServiceFactory
