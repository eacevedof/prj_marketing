<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\CacheQueryTrait
 * @file CacheQueryTrait.php 1.0.0
 * @date 01-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

use App\Shared\Infrastructure\Factories\Specific\RedisFactory;

trait CacheQueryTrait
{
    protected function _getCachedResult(string $query, string $table = ""): array
    {
        return RedisFactory::getInstance()->setKey($query)->getQueryResult($table);
    }

    protected function _addToCache(string $query, array $array, float $ttl = 300, string $table = ""): void
    {
        RedisFactory::getInstance()->setTtl($ttl)->setKey($query)->setValue($array)->saveQuery($table);
    }

    protected function _getCachedSingle(string $query): string
    {
        return RedisFactory::getInstance()->setKey($query)->getQuerySingle();
    }

    protected function _addToCacheSingle(string $query, string $value, float $ttl = 300): void
    {
        RedisFactory::getInstance()->setTtl($ttl)->setKey($query)->setValue($value)->saveQuerySingle();
    }

    protected function _getCachedCount(string $query, string $table = ""): int
    {
        return RedisFactory::getInstance()->setKey($query)->getQueryCount($table);
    }

    protected function _addToCacheCount(string $query, int $count, float $ttl = 300, string $table = ""): void
    {
        RedisFactory::getInstance()->setTtl($ttl)->setKey($query)->setValue($count)->saveQueryCount($table);
    }

    protected function _cacheDelQueryAndCount(string $query, string $table = ""): void
    {
        RedisFactory::getInstance()->setKey($query)->deleteQueryAndCount($table);
    }

    protected function _cacheDeleteAll(string $table): void
    {
        RedisFactory::getInstance()->deleteAllHavingTable($table);
    }
}//CacheQueryTrait
