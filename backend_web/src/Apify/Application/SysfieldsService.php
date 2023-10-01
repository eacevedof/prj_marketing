<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Mysql
 * @file SysfieldsService.php 1.0.0
 * @date 27-06-2019 17:55 SPAIN
 * @observations
 */

namespace App\Apify\Application;

use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\CacheQueryTrait;
use TheFramework\Components\Db\Context\ComponentContext;

final class SysfieldsService extends AppService
{
    use CacheQueryTrait;

    private const USER_UUID_KEY = "useruuid";
    private const USER_TABLE = "base_user";
    private const UUID_FIELD = "code_cache";

    private array $params;
    private string $targettable;
    private string $codecachevalue;
    private string $action;
    private string $dbname;
    private $oBehav;

    private const ACTION_INSERT = "insert";
    private const ACTION_UPDATE = "update";
    private const ACTION_DELETE = "deletelogic";

    private const INSERT_FIELDS = ["insert_user","insert_date","insert_platform","code_cache"];
    private const UPDATE_FIELDS = ["update_user","update_date","update_platform"];
    private const DELETE_FIELDS = ["delete_user","delete_date","delete_platform"];

    public function __construct(
        string $targettable,
        string $idContext = "",
        string $dbname = "",
        string $action = "",
        array $params
    ) {
        $this->params = $params;
        $this->targettable = $targettable;
        $this->dbname = $dbname;
        $this->action = $action;

        $codecache = $params[self::USER_UUID_KEY] ?? "";
        $this->codecachevalue = str_replace(["'","%"," "], "", $codecache);

        $ctx = new ComponentContext($_ENV["APP_CONTEXTS"], $idContext);
        $oDb = DbFactory::getMysqlInstanceByConfiguredContextAndDbName($ctx, $dbname);
        $this->oBehav = new SchemaBehaviour($oDb);
    }

    private function _get_table_user(): array
    {
        return $this->oBehav->getTable(self::USER_TABLE, $this->dbname);
    }

    private function _get_target_fields(): array
    {
        return array_column($this->oBehav->getFields($this->targettable, $this->dbname), "field_name");
    }

    private function _get_userid_from_db(): ?string
    {
        $table = self::USER_TABLE;
        $field = self::UUID_FIELD;
        if (!$this->codecachevalue || $this->codecachevalue === "null") {
            return null;
        }
        $sql = "/*_get_userid_from_db*/ SELECT id FROM $table WHERE $field='$this->codecachevalue'";
        if ($id = $this->_getCachedSingle($sql)) {
            return $id;
        }

        $id = $this->oBehav->query($sql, 0, 0);
        $this->_addToCacheSingle($sql, $id, 3600);

        if (!$id) {
            return null;
        }
        return $id;
    }

    private function _get_sysfields_by_action(): array
    {
        switch ($this->action) {
            case self::ACTION_INSERT:
                return self::INSERT_FIELDS;
            case self::ACTION_UPDATE:
                return self::UPDATE_FIELDS;
            case self::ACTION_DELETE:
                return self::DELETE_FIELDS;
        }
    }

    private function _get_platform(): string
    {
        foreach ($this->params["fields"] ?? [] as $field => $value) {
            if(in_array($field, ["insert_platform","update_platform", "delete_platform"])) {
                return $value;
            }
        }
        return "";
    }

    private function _get_autofilled(): array
    {
        //el inner de los campos por accion y los existentes en tabla
        $finalfields = $this->_get_final_fields();

        foreach ($finalfields as $fieldname => $value) {
            if(strstr($fieldname, "_date")) {
                $finalfields[$fieldname] = date("YmdHis");
            }

            if(strstr($fieldname, "_user")) {
                $finalfields[$fieldname] = $this->_get_table_user() ? $this->_get_userid_from_db() : "-1";
            }

            if(strstr($fieldname, "_platform")) {
                $finalfields[$fieldname] = $this->_get_platform();
            }

            if ($fieldname === self::UUID_FIELD) {
                $finalfields[$fieldname] = uniqid();
            }
        }
        return $finalfields;
    }

    private function _get_final_fields(): array
    {
        $allfields = $this->_get_target_fields();
        $sysfields = $this->_get_sysfields_by_action();

        $final = [];
        foreach ($sysfields as $sysfield) {
            if(in_array($sysfield, $allfields)) {
                $final[$sysfield] = "";
            }
        }
        return $final;
    }

    public function get(): array
    {
        if (!in_array($this->action, [self::ACTION_INSERT, self::ACTION_UPDATE, self::ACTION_DELETE])) {
            return [];
        }
        return $this->_get_autofilled();
    }

}//SysfieldsService
