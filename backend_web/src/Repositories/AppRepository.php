<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\AppRepository
 * @file AppRepository.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */
namespace App\Repositories;
use App\Traits\LogTrait;
use App\Models\AppEntity;
use TheFramework\Components\Db\ComponentCrud;
use TheFramework\Components\Db\ComponentMysql;
use App\Enums\ExceptionType;
use \Exception;

abstract class AppRepository
{
    use LogTrait;
    
    protected AppEntity $model;
    protected ComponentMysql $db;
    protected string $table;

    protected function _get_sanitized(string $value): string
    {
        return str_replace("'","\\'", $value);
    }

    protected function _get_crud(): ComponentCrud
    {
        return new ComponentCrud();
    }

    protected function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }

    private function _get_pks($arData)
    {
        $pks = [];
        foreach($arData as $fieldname=>$sValue)
            if(in_array($fieldname,$this->model->get_pks()))
                $pks[$fieldname] = $sValue;
        return $pks;
    }

    private function _get_no_pks($arData)
    {
        $pks = [];
        foreach($arData as $fieldname=>$sValue)
            if(!in_array($fieldname, $this->model->get_pks()))
                $pks[$fieldname] = $sValue;
        return $pks;
    }

    public function set_model(AppEntity $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function query($sSQL,$iCol=NULL,$iRow=NULL)
    {
        $mxRet = $this->db->query($sSQL,$iCol=NULL,$iRow=NULL);
        if($this->db->is_error())
            $this->add_error($this->db->get_errors());
        return $mxRet;
    }

    public function execute($sSQL)
    {
        $mxRet = $this->db->exec($sSQL);
        if($this->db->is_error())
            $this->add_error($this->db->get_errors());
        return $mxRet;
    }

    public function insert(array $request): int
    {
        $crud = $this->_get_crud()
            ->set_comment("app.insert(request)")
            ->set_dbobj($this->db)
            ->set_table($this->table);

        foreach($request as $field => $value)
            $crud->add_insert_fv($field, $value);

        $crud->autoinsert();
        $this->log($crud->get_sql());

        if($crud->is_error()) {
            $this->logerr($request,"insert");
            $this->_exception(__("Error inserting data"));
        }

        return $this->db->get_lastid();
    }//insert

    public function update(array $request): int
    {
        $pks = $this->_get_pks($request);
        if(!$pks) $this->_exception(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $mutables = $this->_get_no_pks($request);
        if(!$mutables)
            $this->_exception(__("No data to update"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $crud = $this->_get_crud()
            ->set_comment("app.update(request)")
            ->set_dbobj($this->db)
            ->set_table($this->table);

        //valores del "SET"
        foreach($mutables as $fieldname=>$sValue)
            $crud->add_update_fv($fieldname, $sValue);

        //valores del WHERE
        foreach($pks as $fieldname=>$sValue)
            $crud->add_pk_fv($fieldname, $sValue);

        $crud->autoupdate();
        $this->log($crud->get_sql());

        if($crud->is_error()) {
            $this->logerr($request,"update");
            $this->_exception(__("Error updating data"));
        }

        return $this->db->get_affected();
    }//update

    public function delete(array $request): int
    {
        $pks = $this->_get_pks($request);
        if(!$pks) $this->_exception(__("No code/s provided"), ExceptionType::CODE_UNPROCESSABLE_ENTITY);

        $crud = $this->_get_crud()
            ->set_comment("app.delete(request)")
            ->set_dbobj($this->db)
            ->set_table($this->table);

        //valores del WHERE
        foreach($pks as $fieldname=>$sValue)
            $crud->add_pk_fv($fieldname, $sValue);

        $crud->autodelete();
        $this->log($crud->get_sql());

        if($crud->is_error()) {
            $this->logerr($request,"delete");
            $this->_exception(__("Error deleting data"));
        }

        return $this->db->get_affected();
    }//delete

    public function get_sysupdate(array $pks): string
    {
        if(!$pks) return "";

        $crud = $this->_get_crud()
            ->set_comment("app.sysupdate")
            ->set_dbobj($this->db)
            ->set_getfields(["m.update_date"])
            ->set_table("$this->table as m")
        ;

        foreach($pks as $fieldname=>$sValue)
            $crud->add_pk_fv($fieldname, $sValue);

        $sql = $crud->get_selectfrom();
        $r = $this->db->query($sql);
        return $r[0]["update_date"] ?? "";
    }
    
    public function get_max($fieldname)
    {
        if($fieldname)
        {
            $sSQL = "SELECT MAX($fieldname) AS maxed FROM $this->table";
            $mxMaxed = $this->db->query($sSQL);
            $mxMaxed = (isset($mxMaxed[0]["maxed"])?$mxMaxed[0]["maxed"]:NULL);
            return $mxMaxed;
        }
        return NULL;
    }

    public function get_lastinsert_id()
    {
        return $this->db->get_lastid();
    }

    public function get_csvcru(array $row, string $iduser): string
    {
        $now = date("Y-m-d H:i:s");
        $crucsv = $row["cru_csvnote"] ?? "";
        $crucsv = "delete_user:{$row["delete_user"]},delete_date:{$row["delete_date"]},delete_platform:{$row["delete_platform"]},($iduser:$now)|".$crucsv;
        return substr($crucsv,0,499);
    }
}//AppRepository
