<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Mysql
 * @file ContextService.php 1.0.0
 * @date 27-06-2019 17:55 SPAIN
 * @observations
 */

namespace App\Apify\Application;

use App\Shared\Infrastructure\Services\AppService;
use TheFramework\Components\Db\Context\ComponentContext;

final class ContextService extends AppService
{
    private $oContext;

    public function __construct()
    {
        $this->oContext = new ComponentContext($_ENV["APP_CONTEXTS"]);
        //bug($this->oContext,"ContextService");
    }

    public function get_context_by_id($id)
    {
        return $this->oContext->getContextByKV("id", $id);
    }
    public function get_pubconfig()
    {
        return $this->oContext->getPublicConfig();
    }
    public function get_pubconfig_by_id($id)
    {
        return $this->oContext->getPublicConfigByKV("id", $id);
    }
    public function is_context($idContext)
    {
        $arContext = $this->oContext->getContextById($idContext);
        //bug($arContext,"is_context");
        return !empty($arContext);
    }

    private function _get_schemas($idContext)
    {
        $arContext = $this->oContext->getContextById($idContext);
        //$this->logd($arContext,"is_db.arcontext ($dbname)");
        //pr($arContext);die;
        $ipos = array_keys($arContext)[0];
        return $arContext[$ipos]["schemas"] ?? [];
    }

    private function _get_schema_by_alias(array $schemas, string $dbalias): array
    {
        foreach ($schemas as $arschema) {
            if ($arschema["alias"] === $dbalias) {
                return $arschema;
            }
        }
        return [];
    }

    private function _get_schema_by_dbname($schemas, $dbname)
    {
        foreach ($schemas as $arschema) {
            if ($arschema["database"] === $dbname) {
                return $arschema;
            }
        }
        return [];
    }

    public function get_db($idContext, $dbalias)
    {
        $schemas = $this->_get_schemas($idContext);
        $schema = $this->_get_schema_by_alias($schemas, $dbalias);
        return $schema["database"] ?? "";
    }

    public function is_db($idContext, $dbname)
    {
        $schemas = $this->_get_schemas($idContext);
        $schema = $this->_get_schema_by_dbname($schemas, $dbname);
        if ($schema) {
            return true;
        }
        //$this->logd("no is in schemas $dbname");
        return false;
    }

}//ContextService
