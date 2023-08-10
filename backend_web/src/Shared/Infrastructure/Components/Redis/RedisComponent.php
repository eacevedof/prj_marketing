<?php

namespace App\Shared\Infrastructure\Components\Redis;

use Redis;

final class RedisComponent
{
    private const REDIS_SERVER = "host.docker.internal";
    private const REDIS_PORT = "6379";
    private static Redis $redis;

    private string $key;
    private mixed $value;
    private float $ttl; //seconds ejemplo 300.5

    public function __construct()
    {
        if (!self::$redis) {
            self::$redis = new Redis;
            self::$redis->connect(self::REDIS_SERVER, self::REDIS_PORT);
        }
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setValue(mixed $mxValue): self
    {
        $this->value = $mxValue;
        return $this;
    }

    public function setTtl(float $seconds): self
    {
        $this->ttl = $seconds;
        return $this;
    }

    public function getValueByKey(): string
    {
        return self::$redis->get($this->key);
    }

    public function save(): void
    {
        self::$redis->set($this->key, $this->value);
    }

    public function set(string $key, string $value, float $expire = 300): void
    {
        self::$redis->set($key, $value);
        self::$redis->expire($key, $expire);
    }

    public function get(string $key)
    {
        return self::$redis->get($key);
    }

    public function saveQuery(string $table = ""): self
    {
        $prefix = "query-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash = $prefix.md5($this->key);
        $json = json_encode($this->value);
        self::$redis->set($hash, $json);
        self::$redis->expire($hash, $this->ttl);
        return $this;
    }

    public function getQueryResult(string $table = ""): array
    {
        $prefix = "query-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash = $prefix.md5($this->key);
        $json = self::$redis->get($hash);
        if (!$json) {
            return [];
        }
        $array = json_decode($json, true, JSON_UNESCAPED_UNICODE);
        return $array;
    }

    public function saveQueryCount(string $table = ""): self
    {
        $prefix = "query-count-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash = $prefix.md5($this->key);
        self::$redis->set($hash, (int) $this->value);
        self::$redis->expire($hash, $this->ttl);
        return $this;
    }

    public function getQueryCount(string $table = ""): int
    {
        $prefix = "query-count-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash = $prefix.md5($this->key);
        $count = self::$redis->get($hash);
        return (int) $count;
    }

    public function deleteQueryAndCount(string $table = ""): void
    {
        $prefix = "query-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash1 = $prefix.md5($this->key);

        $prefix = "query-count-";
        if ($table) {
            $prefix .= "$table-";
        }
        $hash2 = $prefix.md5($this->key);

        self::$redis->del($hash1, $hash2);
    }

    public function deleteAllHavingTable(string $table): void
    {
        $keys1 = self::$redis->keys("query-$table-*");
        $keys = self::$redis->keys("query-count-$table-*");
        $keys = array_merge($keys1, $keys);
        foreach ($keys as $key) {
            self::$redis->del($key);
        }
    }

    public function getQuerySingle(): string
    {
        $hash = "query-single-".md5($this->key);
        if (!$value = self::$redis->get($hash)) {
            return "";
        }
        return $value;
    }

    public function saveQuerySingle(): self
    {
        $hash = "query-single-".md5($this->key);
        self::$redis->set($hash, $this->value);
        self::$redis->expire($hash, $this->ttl);
        return $this;
    }
}
