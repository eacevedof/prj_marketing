<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Dbs\SchemaService
 * @file SchemaService.php 1.0.0
 * @date 28-01-2019 19:00 SPAIN
 * @observations
 */
namespace App\Services\Dbs;

use App\Behaviours\SchemaBehaviour;

final class SchemaService
{
    private SchemaBehaviour $behavschema;

    public function __construct(?Object $db=null)
    {
        //necesitaria un objeto de db
        $this->behavschema = new SchemaBehaviour($db);
    }

    public function get_tables(): array
    {
        return $this->behavschema->get_tables();
    }

    public function get_fields_info(string $table): array
    {
        return $this->behavschema->get_fields_info($table);
    }

    public function get_tables_info(string $tables=""): array
    {
        $artables = $tables ? explode(",",$tables) : $this->get_tables();
        $return = [];
        foreach ($artables as $table)
            $return[$table] = $this->get_fields_info($table);
        return $return;
    }

}//SchemaService