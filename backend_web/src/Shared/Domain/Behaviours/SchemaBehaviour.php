<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Behaviours\SchemaBehaviour
 * @file SchemaBehaviour.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Domain\Behaviours;

use Exception;
use App\Dbs\Application\CoreQueriesService;
use TheFramework\Components\Db\ComponentMysql;
use App\Shared\Infrastructure\Traits\{CacheQueryTrait, ErrorTrait};

final class SchemaBehaviour
{
    use CacheQueryTrait;
    use ErrorTrait;

    private ?ComponentMysql $componentMysql;
    private CoreQueriesService $coreQueriesService;
    private int $foundRows = 0;
    private bool $doUseCache = false;
    private const CACHE_TIME = 3600;

    public function __construct(?ComponentMysql $componentMysql = null)
    {
        $this->componentMysql = $componentMysql ?? new ComponentMysql;
        $this->coreQueriesService = new CoreQueriesService;
    }

    private function _getOrderByPosition(string $sql): ?int
    {
        $find = [" ORDER BY ", " ORDER BY\n", "\nORDER BY\n", "ORDER BY\n", "\nORDER BY ", "\nORDER BY"];
        foreach ($find as $orderby) {
            $pos = strrpos($sql, $orderby, -1);
            if ($pos !== false) {
                return $pos;
            }
        }
        return null;
    }

    private function _getLimit(string $sql): ?int
    {
        $find = [" LIMIT ", " LIMIT\n", "\nLIMIT\n", "LIMIT\n", "\nLIMIT ", "\nLIMIT"];
        foreach ($find as $limit) {
            $pos = strrpos($sql, $limit, -1);
            if ($pos !== false) {
                return $pos;
            }
        }
        return null;
    }

    private function _getQueryCount(string $sql): string
    {
        if ($to = $this->_getOrderByPosition($sql)) {
            $sql = substr($sql, 0, $to);
        }

        if ($to = $this->_getLimit($sql)) {
            $sql = substr($sql, 0, $to);
        }

        return "/*count-query*/SELECT COUNT(*) FROM ($sql) AS c";
    }

    public function query(string $sql, ?int $colIdx = null, ?int $rowIdx = null): array
    {
        try {
            $sqlCount = $this->_getQueryCount($sql);
            $r = $this->componentMysql
                ->set_sqlcount($sqlCount)
                ->query($sql, $colIdx, $rowIdx)
            ;
            //to-do esto va a fallar pq ya no se usa calcrows
            $this->foundRows = $this->componentMysql->get_foundrows();
            return $r;
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
            return [];
        }
    }

    public function execute(string $sql): false|int
    {
        try {
            return $this->componentMysql->exec($sql);
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
            return false;
        }
    }

    public function getSchemas(): array
    {
        $sql = "
        -- get_schemas
        SELECT schema_name as dbname
        FROM information_schema.schemata
        ORDER BY schema_name;
        ";
        if (!$this->doUseCache) {
            return $this->query($sql);
        }

        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function getTables(string $dbname = ""): array
    {
        $sql = $this->coreQueriesService->getTables($dbname);
        if (!$this->doUseCache) {
            return $this->query($sql);
        }

        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function getTable(string $table, string $dbname = ""): array
    {
        $sql = $this->coreQueriesService->getTables($dbname, $table);
        if (!$this->doUseCache) {
            return $this->query($sql);
        }
        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql, 0);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function getFieldsInfo(string $table, string $dbname = ""): array
    {
        $sql = $this->coreQueriesService->getFields($dbname, $table);
        if (!$this->doUseCache) {
            return $this->query($sql);
        }
        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function getFields(string $table, string $dbname = ""): array
    {
        $sql = $this->coreQueriesService->getFieldsMin($dbname, $table);
        if (!$this->doUseCache) {
            return $this->query($sql);
        }
        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function getFieldInfo(string $field, string $table, string $dbname = ""): array
    {
        $sql = $this->coreQueriesService->getField($dbname, $table, $field);
        if (!$this->doUseCache) {
            return $this->query($sql);
        }
        if ($r = $this->_getCachedResult($sql)) {
            return $r;
        }
        $r = $this->query($sql);
        $this->_addToCache($sql, $r, self::CACHE_TIME);
        return $r;
    }

    public function useCache(bool $use = true): self
    {
        $this->doUseCache = $use;
        return $this;
    }

    public function execRawReadQuery(string $sql): array
    {
        return $this->query($sql);
    }
    public function execRawWriteQuery(string $sql): false |int
    {
        return $this->execute($sql);
    }
    public function getCountRows(): int
    {
        return $this->foundRows;
    }
    public function getLastInsertId(): int
    {
        return $this->componentMysql->get_lastid();
    }

}//SchemaBehaviour
