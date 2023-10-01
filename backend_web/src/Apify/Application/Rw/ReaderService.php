<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Rw\ReaderService
 * @file ReaderService.php 1.0.0
 * @date 27-06-2019 17:55 SPAIN
 * @observations
 */

namespace App\Apify\Application\Rw;

use TheFramework\Components\Db\ComponentQB;
use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\CacheQueryTrait;
use TheFramework\Components\Db\Context\ComponentContext;

final class ReaderService extends AppService
{
    use CacheQueryTrait;

    private string $idContext;
    private string $dbName;

    private ComponentContext $componentContext;
    private SchemaBehaviour $schemaBehaviour;
    private string $sql;
    private int $foundRows;
    private float $cacheTtl = 0;
    private string $mainTable = "";

    public function __construct(string $idContext = "", string $dbAlias = "")
    {
        $this->idContext = $idContext;

        if (!$this->idContext) {
            $this->_addError("Error in context: $idContext");
            return;
        }

        $this->componentContext = new ComponentContext($_ENV["APP_CONTEXTS"], $idContext);
        $this->dbName = $this->componentContext->getDbNameByAlias($dbAlias);

        $componentMysql = DbFactory::getMysqlInstanceByConfiguredContextAndDbName(
            $this->componentContext,
            $this->dbName
        );
        $this->schemaBehaviour = new SchemaBehaviour($componentMysql);
    }

    private function _getSqlFromQueryConfig(array $queryConfig): string
    {
        if (!isset($queryConfig["fields"]) || !is_array($queryConfig["fields"])) {
            $this->_throwException("invalid or empty fields in read params");
        }

        $queryBuilder = new ComponentQB;
        if ($queryConfig["comment"] ?? "") {
            $queryBuilder->set_comment($queryConfig["comment"]);
        }

        $queryBuilder->set_table($queryConfig["table"]);
        if(isset($queryConfig["distinct"])) {
            $queryBuilder->distinct((bool) $queryConfig["distinct"]);
        }
        if(isset($queryConfig["foundrows"])) {
            $queryBuilder->calcfoundrows((bool) $queryConfig["foundrows"]);
        }

        $queryBuilder->set_getfields($queryConfig["fields"]);
        $queryBuilder->set_joins($queryConfig["joins"] ?? []);
        $queryBuilder->set_and($queryConfig["where"] ?? []);
        $queryBuilder->set_groupby($queryConfig["groupby"] ?? []);
        $queryBuilder->set_having($queryConfig["having"] ?? []);

        if(isset($queryConfig["orderby"])) {
            foreach($queryConfig["orderby"] as $sField) {
                $fields = explode(" ", trim($sField));
                $queryBuilder->add_orderby($fields[0], $fields[1] ?? "ASC");
            }
        }

        if(isset($queryConfig["limit"]["perpage"])) {
            $queryBuilder->set_limit($queryConfig["limit"]["perpage"] ?? 1000, $queryConfig["limit"]["regfrom"] ?? 0);
        }

        $queryBuilder->select();
        return $queryBuilder->sql();
    }

    public function execRawSql(string $sql): array
    {
        //intento leer cache
        if ($ttl = $this->cacheTtl) {
            if ($r = $this->_getCachedResult($sql, $this->mainTable)) {
                $this->foundRows = $this->_getCachedCount($sql, $this->mainTable);
                return $r;
            }
        }

        //leo de bd
        $r = $this->schemaBehaviour->execRawReadQuery($sql);
        $this->foundRows = $this->schemaBehaviour->getCountRows();
        if ($this->schemaBehaviour->isError()) {
            if ($ttl) {
                $this->_cacheDelQueryAndCount($sql, $this->mainTable);
            }
            $this->logErr($errors = $this->schemaBehaviour->getErrors(), "readservice.read_raw");
            $this->_addErrors($errors);
            return $r;
        }

        //la consulta ha ido bien. guardo en cache
        if ($ttl) {
            $this->_addToCache($sql, $r, $ttl, $this->mainTable);
            $this->_addToCacheCount($sql, $this->foundRows, $ttl, $this->mainTable);
        }
        return $r;
    }

    public function execSqlByConfig(array $queryConfig): array
    {
        if (!is_array($queryConfig)) {
            $this->_throwException("read params is not an array");
        }
        if (!$table = trim($queryConfig["table"])) {
            $this->_throwException("missing read table");
        }

        $this->mainTable = explode(" ", $table)[0];
        $this->cacheTtl = (int) $queryConfig["cache_time"] ?? 0;
        $sql = $this->_getSqlFromQueryConfig($queryConfig);
        $r = $this->execRawSql($sql);
        return $r;
    }

    public function getFoundRows(): int
    {
        return $this->foundRows;
    }

}//ReaderService
