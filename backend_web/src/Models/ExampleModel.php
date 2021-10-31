<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\ExampleModel 
 * @file ExampleModel.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Models;

final class ExampleModel extends AppModel
{
    public function __construct() 
    {
        $this->table = "titles";
        parent::__construct();
        $this->load_pk_fields();
        $this->load_fileds();
    }
        
    private function load_fileds()
    {
        $arTmp = [
            ["db"=>"emp_no","ui"=>"empno"],
            ["db"=>"title","ui"=>"utitle"],
            ["db"=>"from_date","ui"=>"fromdate"],
            ["db"=>"to_date","ui"=>"todate"]
        ];
        $this->fields = $arTmp;
    }//load_fileds
    
    private function load_pk_fields()
    {
        $this->pks = ["emp_no","title","from_date"];
    }//load_pk_fields
    
    // carga combo
    public function get_picklist()
    {
        $sSQL = "
        /*ExampleModel.get_picklist*/
        SELECT DISTINCT title,title
        FROM titles
        ORDER BY 2
        ";
        $arRows = $this->db->query($sSQL);
        return $arRows;
    }//get_picklist
    
}//ExampleModel
