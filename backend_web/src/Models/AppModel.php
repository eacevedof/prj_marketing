<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\AppModel 
 * @file AppModel.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */
namespace App\Models;

use TheFramework\Components\Db\ComponentMysql;
use TheFramework\Components\Db\ComponentCrud;

use App\Traits\ErrorTrait;
use App\Traits\LogTrait;

abstract class AppModel
{
    use ErrorTrait;
    use LogTrait;
    
    protected ComponentMysql $db;
    protected string $table;
    protected array $fields;
    protected array $pks;
    
    public function __construct(ComponentMysql $db=NULL) 
    {
        $this->db = $db;
        if(!$this->db)
            throw new \Exception("No ddbobject in AppModel");
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

    public function get_max($sField)
    {
        if($sField)
        {
            $sSQL = "SELECT MAX($sField) AS maxed FROM $this->table";
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
    protected function get_keyvals($arPost)
    {
        $fieldsUi = array_keys($arPost);
        $arReturn = [];
        foreach($this->fields as $arMap)
        {
            $sFieldDb = $arMap["db"];
            $sFieldUi = $arMap["ui"];
            if(in_array($sFieldUi,$fieldsUi))
                $arReturn[$sFieldDb] = $arPost[$sFieldUi];
        }
        return $arReturn;
    }

    //hace un insert automatico a partir de lo que viene en $_POST
    public function insert($arPost,$isUi=1)
    {
        $arData = $arPost;
        if($isUi)
            $arData = $this->get_keyvals($arPost);

        //print_r($arData);die;
        if($arData)
        {
            //helper generador de consulta.
            //se le inyecta el objeto de bd para que la ejecute directamente
            $oCrud = new ComponentCrud($this->db);
            $oCrud->set_table($this->table);
            foreach($arData as $sFieldName=>$sValue)
                $oCrud->add_insert_fv($sFieldName,$sValue);
            $oCrud->autoinsert();
            //print_r($oCrud);die;
            $this->log($oCrud->get_sql());
            if($oCrud->is_error())
                $this->add_error("An error occurred while trying to save");
            $this->log($oCrud->get_sql(),($oCrud->is_error()?"ERROR":NULL));
        }
    }//insert

    private function _get_pks($arData)
    {
        $pks = [];
        foreach($arData as $sFieldName=>$sValue)
            if(in_array($sFieldName,$this->pks))
                $pks[$sFieldName] = $sValue;
        return $pks;
    }

    private function _get_no_pks($arData)
    {
        $pks = [];
        foreach($arData as $sFieldName=>$sValue)
            if(!in_array($sFieldName,$this->pks))
                $pks[$sFieldName] = $sValue;
        return $pks;
    }

    public function update($arPost, $isUi=1)
    {
        $arData = $arPost;
        if($isUi)
            $arData = $this->get_keyvals($arPost);

        $arNoPks = $this->_get_no_pks($arData);
        $pks = $this->_get_pks($arData);

        if ($arData) {
            //habría que comprobar count(pks)==count($this->pks)
            $oCrud = new ComponentCrud($this->db);
            $oCrud->set_table($this->table);

            //valores del "SET"
            foreach($arNoPks as $sFieldName=>$sValue)
                $oCrud->add_update_fv($sFieldName,$sValue);

            //valores del WHERE
            foreach($pks as $sFieldName=>$sValue)
                $oCrud->add_pk_fv($sFieldName,$sValue);

            $oCrud->autoupdate();
            if($oCrud->is_error())
                $this->add_error("An error occurred while trying to delete");

            $this->log($oCrud->get_sql(),($oCrud->is_error()?"ERROR":NULL));
        }
    }//update

    public function delete($arPost)
    {
        $arData = $this->get_keyvals($arPost);
        $pks = $this->_get_pks($arData);
        if($pks)
        {
            $oCrud = new ComponentCrud($this->db);
            $oCrud->set_table($this->table);
            foreach($pks as $sFieldName=>$sValue)
                $oCrud->add_pk_fv($sFieldName,$sValue);
            $oCrud->autodelete();

            if($oCrud->is_error())
                $this->add_error("An error occurred while trying to delete");

            $this->log($oCrud->get_sql(),($oCrud->is_error()?"ERROR":NULL));
        }
    }//delete
    
    /**
     * Se usa antes de borrar o actualizar
     * Se pasa el post y comprueba que existan todos los campos clave
     * @param array $arPost ["uifield"=>"value" ...]
     * @return boolean
     */
    public function is_pks_ok($arPost)
    {
        $arData = $this->get_keyvals($arPost);
        $pks = $this->_get_no_pks($arData);
        return (count($pks)===count($this->pks));
    }
        
    public function set_table($table){$this->table = $table;}
    public function add_pk($sFieldName){$this->pks[] = $sFieldName;}
    public function set_pk($sFieldName){$this->pks = []; $this->pks[] = $sFieldName;}

    public function get_table(){return $this->table;}
    public function get_fields(){return $this->fields;}
    public function get_pks(){return $this->pks;}
}//AppModel
