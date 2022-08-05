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

use TheFramework\Components\Db\Context\ComponentContext;
use TheFramework\Components\Db\ComponentQB;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;
use App\Apify\Application\SysfieldsService;
use App\Shared\Infrastructure\Traits\CacheQueryTrait;

final class WriterService extends AppService
{
    use CacheQueryTrait;

    private const ACTIONS = ["insert", "update", "delete", "deletelogic", "drop", "alter"];

    private array $fields;

    private string $idcontext;
    private string $dbname;

    private $oContext;
    private $oBehav;
    
    private string $action;
    private string $maintable;

    public function __construct(string $idcontext="", string $dbalias="", string $table="")
    {
        if(!$idcontext) $this->_exception("no context provided");
        if(!$dbalias) $this->_exception("no db-alias received");
        
        //table puede ir vacio si se desea ejecutar una consulta en crudo (raw)
        //if(!$table) $this->_exception("no table received");

        $this->idcontext = $idcontext;

        $this->oContext = new ComponentContext($this->get_env("APP_CONTEXTS"), $idcontext);
        $this->dbname = $this->oContext->get_dbname($dbalias);
        $db = DbFactory::get_dbobject_by_ctx($this->oContext, $this->dbname);
        $this->oBehav = new SchemaBehaviour($db);
        $this->fields = array_column($this->oBehav->get_fields($table, $this->dbname),"field_name");
    }

    /**
     * elimina posibles escrituras directas en otros campos
     * @param $qparams
     * @param $action
     */
    private function _unset_sysfields(&$qparams, $action): void
    {
        $issysfields = $qparams["autosysfields"] ?? 0;
        if($issysfields)
        {
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

            foreach ($arUnset as $fieldname)
                if (isset($qparams["fields"][$fieldname]))
                    unset($qparams["fields"][$fieldname]);
        }
    }

    private function _get_insert_sql(array $qparams): string
    {
        if(!isset($qparams["fields"])) $this->_exception("_get_insert_sql no fields");

        $oCrud = new ComponentQB();
        $oCrud->set_comment(str_replace(["*","/",],"",trim($qparams["comment"])));
        $oCrud->set_table($qparams["table"]);
        foreach($qparams["fields"] as $sFieldName=>$sFieldValue)
            if($sFieldValue==="null")
                $oCrud->add_insert_fv($sFieldName,null,0);
            else
                $oCrud->add_insert_fv($sFieldName,$sFieldValue);

        $this->_add_sysfields($oCrud, $qparams);
        if(in_array("update_date",$this->fields))
            $oCrud->add_insert_fv("update_date",null,0);

        $oCrud->insert();
        
        return $oCrud->sql();
    }

    private function _get_update_sql(array $qparams): string
    {
        if(!isset($qparams["fields"])) $this->_exception("_get_update_sql no fields");
        //if(!isset($qparams["pks"])) return $this->add_error("_get_update_sql no pks");

        $oCrud = new ComponentQB();
        $oCrud->set_comment(str_replace(["*","/",],"",$qparams["comment"]));
        $oCrud->set_table($qparams["table"]);

        foreach($qparams["fields"] as $sFieldName=>$sFieldValue)
            if($sFieldValue==="null")
                $oCrud->add_update_fv($sFieldName,null,0);
            else
                $oCrud->add_update_fv($sFieldName,$sFieldValue);

        $this->_add_sysfields($oCrud, $qparams);

        if(isset($qparams["pks"]))
            foreach($qparams["pks"] as $sFieldName=>$sFieldValue)
                $oCrud->add_pk_fv($sFieldName,$sFieldValue);


        if(isset($qparams["where"]))
            foreach($qparams["where"] as $sWhere)
                $oCrud->add_and($sWhere);


        $oCrud->update();
        $sql = $oCrud->sql();
        //pr($sql);die;
        return $sql;
    }//_get_update_sql

    private function _get_delete_sql(array $qparams): string
    {
        $oCrud = new ComponentQB();
        $oCrud->set_comment(str_replace(["*","/",],"",$qparams["comment"]));
        $oCrud->set_table($qparams["table"]);
        if(isset($qparams["where"]))
            foreach($qparams["where"] as $sWhere)
            {
                $oCrud->add_and($sWhere);
            }        
        $oCrud->delete();
        $sql = $oCrud->sql();
        
        return $sql;      
    }//_get_delete_sql

    private function _get_deletelogic_sql(array $qparams): string
    {
        $oCrud = new ComponentQB();
        $oCrud->set_comment(str_replace(["*","/",],"",$qparams["comment"]));
        $oCrud->set_table($qparams["table"]);
        $this->_add_sysfields($oCrud, $qparams);

        $oCrud->add_update_fv("delete_platform",$qparams["fields"]["delete_platform"]);
        //como el registro tiene el trigger del update si quiero marcar el softdelete tambien actualizaría el update_date
        //si paso en formato de tags obligo que el update_date=update_date es decir se mantenga el update_date anterior
        $oCrud->add_update_fv("update_date","%%update_date%%",0);

        if(isset($qparams["pks"]))
            foreach($qparams["pks"] as $sFieldName=>$sFieldValue)
            {
                $oCrud->add_pk_fv($sFieldName,$sFieldValue);
            }

        if(isset($qparams["where"]))
            foreach($qparams["where"] as $sWhere)
            {
                $oCrud->add_and($sWhere);
            }

        $oCrud->update();
        $sql = $oCrud->sql();
        //pr($sql);die;
        return $sql;
    }//_get_deletelogic_sql

//==================================
//      PUBLIC
//==================================
    public function write_raw(string $sql)
    {
        $r = $this->oBehav->write_raw($sql);
        if($this->oBehav->is_error()) {
            $this->add_error($this->oBehav->get_errors());
            return -1;
        }
        //si todo ha ido bien refresco cache
        $this->cache_del_all($this->maintable);
        return $r;
    }

    private function _add_sysfields(ComponentQB $oCrud, $qparams): void
    {
        if(!($qparams["autosysfields"] ?? false)) return;

        $sysfields = (
            new SysfieldsService($this->maintable, $this->idcontext, $this->dbname, $this->action, $qparams)
        )->get();

        foreach ($sysfields as $sysfield=>$value)
        {
            if(in_array($this->action, ["update", "deletelogic"]))
                $oCrud->add_update_fv($sysfield, $value);
            if($this->action==="insert")
                $oCrud->add_insert_fv($sysfield, $value);
        }
    }

    private function _get_parsed_tosql(array $qparams): string
    {
        switch ($action = $this->action)
        {
            case "insert":
                $this->_unset_sysfields($qparams, $action);
                return $this->_get_insert_sql($qparams);
            case "update":
                $this->_unset_sysfields($qparams, $action);
                return $this->_get_update_sql($qparams);
            case "delete":
                return $this->_get_delete_sql($qparams);
            case "deletelogic":
                $this->_unset_sysfields($qparams, $action);
                return $this->_get_deletelogic_sql($qparams);
        }
    }

    public function write(array $qparams)
    {
        if(!is_array($qparams)) $this->_exception("write params is not an array");
        if(!$this->maintable = $qparams["table"]) $this->_exception("missing write table");
        if(!in_array($action = $this->action, self::ACTIONS))
            $this->_exception("action {$action} not recognized!");

        $sql = $this->_get_parsed_tosql($qparams, $this->action);
        return $this->write_raw($sql);
    }

    public function get_lastinsert_id(){return $this->oBehav->get_lastinsert_id();}

    public function set_action($action){$this->action = $action; return $this;}
}//WriterService
