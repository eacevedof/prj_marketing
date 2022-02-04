<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Factories\RepositoryFactory 
 * @file RepositoryFactory.php v1.0.0
 * @date 25-06-2021 19:50 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Factories;

use App\Shared\Domain\Repositories\AppRepository;

final class RepositoryFactory
{
    public static function get(string $repository): ?AppRepository
    {
        return new $repository();
    }

}//RepositoryFactory
