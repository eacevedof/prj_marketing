<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Apify\Application\Mysql
 * @file FieldsService.php 1.0.0
 * @date 27-06-2019 17:55 SPAIN
 * @observations
 */
namespace App\Apify\Application;

use TheFramework\Components\Db\Context\ComponentContext;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Domain\Behaviours\SchemaBehaviour;
use App\Shared\Infrastructure\Factories\DbFactory;

final class FieldsService extends AppService
{
    private $idContext;
    private $sDb;
    private $sTableName;
    private $sFieldName;
    
    private $oContext;
    private $oBehav;
    
    public function __construct($idContext="",$sDb="",$sTable="",$sFieldName="") 
    {
        $this->idContext = $idContext;
        $this->sDb = $sDb;
        $this->sTableName = $sTable;
        $this->sFieldName = $sFieldName;
        
        $this->oContext = new ComponentContext($_ENV["APP_CONTEXTS"],$idContext);
        $oDb = DbFactory::get_dbobject_by_ctx($this->oContext,$sDb);
        $this->oBehav = new SchemaBehaviour($oDb);
    }
        
    public function get_all($sTableName)
    {
        return $this->oBehav->get_fields_info($sTableName,$this->sDb);
    }
    
    public function get_field($sTableName,$sFieldName)
    {
        return $this->oBehav->get_field_info($sFieldName,$sTableName,$this->sDb);
    }    
    
}//FieldsService
