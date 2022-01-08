<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleEntity
 * @file ExampleEntity.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models;

final class ExampleEntity extends AppEntity
{
    public function __construct() 
    {
        $this->table = "titles";
        $this->_load_pk_fields()->_load_fileds();
    }
        
    private function _load_fileds(): self
    {
        $arTmp = [
            ["db"=>"emp_no","ui"=>"empno"],
            ["db"=>"title","ui"=>"utitle"],
            ["db"=>"from_date","ui"=>"fromdate"],
            ["db"=>"to_date","ui"=>"todate"]
        ];
        $this->fields = $arTmp;
        return $this;
    }//load_fileds
    
    private function _load_pk_fields(): self
    {
        $this->pks = ["emp_no","title","from_date"];
        return $this;
    }

}//ExampleEntity
