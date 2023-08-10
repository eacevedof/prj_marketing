<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Mysql
 * @file WriterService.php 1.0.0
 * @date 30-06-2019 12:42 SPAIN
 * @observations
 */

namespace App\Apify\Application\Rw;

use App\Apify\Application\SysfieldsService;
use TheFramework\Components\Db\ComponentQB;
use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\CacheQueryTrait;
use TheFramework\Components\Db\Context\ComponentContext;

final class WriterService extends AppService
{
    use CacheQueryTrait;

    private const ACTIONS = ["insert", "update", "delete", "deletelogic", "drop", "alter"];

    private array $fields;

    private string $idContext;
    private string $dbName;

    private ComponentContext $componentContext;
    private SchemaBehaviour $schemaBehaviour;

    private string $action;
    private string $mainTable;

    public function __construct(string $idContext = "", string $dbAlias = "", string $table = "")
    {
        if (!$idContext) {
            $this->_throwException("no context provided");
        }
        if (!$dbAlias) {
            $this->_throwException("no db-alias received");
        }

        $this->idContext = $idContext;
        $this->componentContext = new ComponentContext($this->getEnvValue("APP_CONTEXTS"), $idContext);
        $this->dbName = $this->componentContext->getDbNameByAlias($dbAlias);
        $mysqlComponent = DbFactory::getMysqlInstanceByConfiguredContextAndDbName($this->componentContext, $this->dbName);
        $this->schemaBehaviour = new SchemaBehaviour($mysqlComponent);
        $this->fields = array_column($this->schemaBehaviour->getFields($table, $this->dbName), "field_name");
    }

    /**
     * elimina posibles escrituras directas en otros campos
     * @param $queryConfig
     * @param $action
     */
    private function _unsetSysFields(array &$queryConfig, string $action): void
    {
        $isAutoSysFields = $queryConfig["autosysfields"] ?? 0;
        if ($isAutoSysFields) {
            switch ($action) {
                case "insert":
                    $arUnset = ["update_date", "update_user", "update_platform", "delete_date", "delete_user", "delete_platform"];
                    break;
                case "update":
                    $arUnset = ["insert_date", "insert_user", "insert_platform", "delete_date", "delete_user", "delete_platform"];
                    break;
                case "deletelogic":
                    $arUnset = ["insert_date", "insert_user", "insert_platform", "update_date", "update_user", "update_platform"];
                    break;
                default:
                    $arUnset = [];
            }

            foreach ($arUnset as $fieldName) {
                if (isset($queryConfig["fields"][$fieldName])) {
                    unset($queryConfig["fields"][$fieldName]);
                }
            }
        }
    }

    private function _getInsertSqlFromQueryConfig(array $queryConfig): string
    {
        if (!isset($queryConfig["fields"])) {
            $this->_throwException("_get_insert_sql no fields");
        }

        $oCrud = new ComponentQB;
        $oCrud->set_comment(str_replace(["*","/",], "", trim($queryConfig["comment"])));
        $oCrud->set_table($queryConfig["table"]);
        foreach($queryConfig["fields"] as $sFieldName => $sFieldValue) {
            if ($sFieldValue === "null") {
                $oCrud->add_insert_fv($sFieldName, null, 0);
            } else {
                $oCrud->add_insert_fv($sFieldName, $sFieldValue);
            }
        }

        $this->_addSysFields($oCrud, $queryConfig);
        if(in_array("update_date", $this->fields)) {
            $oCrud->add_insert_fv("update_date", null, 0);
        }

        $oCrud->insert();
        return $oCrud->sql();
    }

    private function _getUpdateSqlFromQueryConfig(array $queryConfig): string
    {
        if (!isset($queryConfig["fields"])) {
            $this->_throwException("_get_update_sql no fields");
        }
        //if (!isset($queryConfig["pks"])) return $this->add_error("_get_update_sql no pks");

        $oCrud = new ComponentQB;
        $oCrud->set_comment(str_replace(["*","/",], "", $queryConfig["comment"]));
        $oCrud->set_table($queryConfig["table"]);

        foreach($queryConfig["fields"] as $sFieldName => $sFieldValue) {
            if ($sFieldValue === "null") {
                $oCrud->add_update_fv($sFieldName, null, 0);
            } else {
                $oCrud->add_update_fv($sFieldName, $sFieldValue);
            }
        }

        $this->_addSysFields($oCrud, $queryConfig);

        if(isset($queryConfig["pks"])) {
            foreach($queryConfig["pks"] as $sFieldName => $sFieldValue) {
                $oCrud->add_pk_fv($sFieldName, $sFieldValue);
            }
        }


        if(isset($queryConfig["where"])) {
            foreach($queryConfig["where"] as $sWhere) {
                $oCrud->add_and($sWhere);
            }
        }


        $oCrud->update();
        $sql = $oCrud->sql();
        //pr($sql);die;
        return $sql;
    }//_get_update_sql

    private function _getDeleteSqlFromQueryConfig(array $queryConfig): string
    {
        $oCrud = new ComponentQB;
        $oCrud->set_comment(str_replace(["*","/",], "", $queryConfig["comment"]));
        $oCrud->set_table($queryConfig["table"]);
        if(isset($queryConfig["where"])) {
            foreach($queryConfig["where"] as $sWhere) {
                $oCrud->add_and($sWhere);
            }
        }
        $oCrud->delete();
        $sql = $oCrud->sql();

        return $sql;
    }//_get_delete_sql

    private function _getDeleteLogicSqlFromQueryConfig(array $queryConfig): string
    {
        $oCrud = new ComponentQB;
        $oCrud->set_comment(str_replace(["*","/",], "", $queryConfig["comment"]));
        $oCrud->set_table($queryConfig["table"]);
        $this->_addSysFields($oCrud, $queryConfig);

        $oCrud->add_update_fv("delete_platform", $queryConfig["fields"]["delete_platform"]);
        //como el registro tiene el trigger del update si quiero marcar el softdelete tambien actualizaría el update_date
        //si paso en formato de tags obligo que el update_date=update_date es decir se mantenga el update_date anterior
        $oCrud->add_update_fv("update_date", "%%update_date%%", 0);

        if(isset($queryConfig["pks"])) {
            foreach($queryConfig["pks"] as $sFieldName => $sFieldValue) {
                $oCrud->add_pk_fv($sFieldName, $sFieldValue);
            }
        }

        if(isset($queryConfig["where"])) {
            foreach($queryConfig["where"] as $sWhere) {
                $oCrud->add_and($sWhere);
            }
        }

        $oCrud->update();
        $sql = $oCrud->sql();
        //pr($sql);die;
        return $sql;
    }//_get_deletelogic_sql

    //==================================
    //      PUBLIC
    //==================================
    public function executeWriteFromRawSql(string $sql): mixed
    {
        $r = $this->schemaBehaviour->execRawWriteQuery($sql);
        if ($this->schemaBehaviour->isError()) {
            $this->_addErrors($this->schemaBehaviour->getErrors());
            return -1;
        }
        //si todo ha ido bien refresco cache
        $this->_cacheDeleteAll($this->mainTable);
        return $r;
    }

    private function _addSysFields(ComponentQB $oCrud, array $queryConfig): void
    {
        if (!($queryConfig["autosysfields"] ?? false)) {
            return;
        }

        $sysFields = (
            new SysfieldsService($this->mainTable, $this->idContext, $this->dbName, $this->action, $queryConfig)
        )->get();

        foreach ($sysFields as $sysField => $sysValue) {
            if(in_array($this->action, ["update", "deletelogic"])) {
                $oCrud->add_update_fv($sysField, $sysValue);
            }
            if ($this->action === "insert") {
                $oCrud->add_insert_fv($sysField, $sysValue);
            }
        }
    }

    private function _getSqlFromQueryConfig(array $queryConfig): string
    {
        switch ($action = $this->action) {
            case "insert":
                $this->_unsetSysFields($queryConfig, $action);
                return $this->_getInsertSqlFromQueryConfig($queryConfig);
            case "update":
                $this->_unsetSysFields($queryConfig, $action);
                return $this->_getUpdateSqlFromQueryConfig($queryConfig);
            case "delete":
                return $this->_getDeleteSqlFromQueryConfig($queryConfig);
            case "deletelogic":
                $this->_unsetSysFields($queryConfig, $action);
                return $this->_getDeleteLogicSqlFromQueryConfig($queryConfig);
        }
        return "";
    }

    public function write(array $queryConfig): mixed
    {
        if (!$this->mainTable = $queryConfig["table"]) {
            $this->_throwException("missing write table");
        }
        if (!in_array($action = $this->action, self::ACTIONS)) {
            $this->_throwException("action {$action} not recognized!");
        }

        $sql = $this->_getSqlFromQueryConfig($queryConfig, $this->action);
        return $this->executeWriteFromRawSql($sql);
    }

    public function getLastInsertId(): int
    {
        return $this->schemaBehaviour->getLastInsertId();
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }
}//WriterService
