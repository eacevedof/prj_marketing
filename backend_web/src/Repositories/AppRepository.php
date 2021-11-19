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
use App\Models\AppModel;
use TheFramework\Components\Db\ComponentCrud;
use TheFramework\Components\Db\ComponentMysql;
use App\Factories\ModelFactory as MF;
use App\Traits\LogTrait;
use \Exception;

abstract class AppRepository
{
    use LogTrait;
    protected AppModel $model;
    protected ComponentMysql $db;
    protected string $table;

    protected function _get_sanitized(string $value)
    {
        return str_replace("'","\\'", $value);
    }

    protected function _get_crud(): ComponentCrud
    {
        return new ComponentCrud();
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

    //$arPost = $_POST
    //busca los campos de form en el post y guarda sus valores
    //en los campos de bd
    private function _get_keyvals($arPost)
    {
        $fieldsUi = array_keys($arPost);
        $arReturn = [];
        $fields = $this->model->get_fields();
        foreach($fields as $mapfields)
        {
            $fieldnameDb = $mapfields["db"];
            $fieldnameUi = $mapfields["ui"];
            if(in_array($fieldnameUi,$fieldsUi))
                $arReturn[$fieldnameDb] = $arPost[$fieldnameUi];
        }
        return $arReturn;
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

    //hace un insert automatico a partir de lo que viene en $_POST
    public function insert($arPost,$isUi=1)
    {
        $arData = $arPost;
        if($isUi)
            $arData = $this->_get_keyvals($arPost);

        //print_r($arData);die;
        if($arData)
        {
            //helper generador de consulta.
            //se le inyecta el objeto de bd para que la ejecute directamente
            //$this->crud = new ComponentCrud($this->db);
            $this->crud->set_dbobj($this->db)->set_table($this->table);
            foreach($arData as $fieldname=>$sValue)
                $this->crud->add_insert_fv($fieldname,$sValue);
            $this->crud->autoinsert();
            //print_r($this->crud);die;
            $this->log($this->crud->get_sql());
            if($this->crud->is_error()) {
                $this->logerr($arPost,"insert");
                $this->_exeption(__("Error saving data"));
            }
            return $this->db->get_lastid();
        }
    }//insert
        
    public function update($arPost, $isUi=1)
    {
        $arData = $arPost;
        if($isUi)
            $arData = $this->_get_keyvals($arPost);

        $arNoPks = $this->_get_no_pks($arData);
        $pks = $this->_get_pks($arData);

        if ($arData) {
            //habría que comprobar count(pks)==count($this->model->get_pks())
            //$this->crud = new ComponentCrud($this->db);
            $this->crud->set_table($this->table);

            //valores del "SET"
            foreach($arNoPks as $fieldname=>$sValue)
                $this->crud->add_update_fv($fieldname,$sValue);

            //valores del WHERE
            foreach($pks as $fieldname=>$sValue)
                $this->crud->add_pk_fv($fieldname,$sValue);

            $this->crud->autoupdate();
            if($this->crud->is_error())
                $this->add_error("An error occurred while trying to delete");

            $this->log($this->crud->get_sql(),($this->crud->is_error()?"ERROR":NULL));
        }
    }//update

    public function delete($arPost)
    {
        $arData = $this->_get_keyvals($arPost);
        $pks = $this->_get_pks($arData);
        if($pks)
        {
            //$this->crud = new ComponentCrud($this->db);
            $this->crud->set_table($this->table);
            foreach($pks as $fieldname=>$sValue)
                $this->crud->add_pk_fv($fieldname,$sValue);
            $this->crud->autodelete();

            if($this->crud->is_error())
                $this->add_error("An error occurred while trying to delete");

            $this->log($this->crud->get_sql(),($this->crud->is_error()?"ERROR":NULL));
        }
    }//delete

    protected function _exeption(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }
}//AppRepository
