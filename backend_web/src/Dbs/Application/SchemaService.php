<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Dbs\Application\SchemaService
 * @file SchemaService.php 1.0.0
 * @date 28-01-2019 19:00 SPAIN
 * @observations
 */

namespace App\Dbs\Application;

use App\Shared\Domain\Behaviours\SchemaBehaviour;

final class SchemaService
{
    private SchemaBehaviour $schemaBehaviour;
    private string $dbname;

    public function __construct(?Object $db = null)
    {
        //necesitaria un objeto de db
        $this->dbname = $db->get_config("database");
        $this->schemaBehaviour = new SchemaBehaviour($db);
    }

    public function getTables(): array
    {
        return $this->schemaBehaviour->getTables($this->dbname);
    }

    public function getFieldsInfo(string $table): array
    {
        return $this->schemaBehaviour->getFieldsInfo($table, $this->dbname);
    }

    public function getTablesInfo(string $csvTables = ""): array
    {
        $tables = $csvTables ? explode(",", $csvTables) : $this->getTables();
        $return = [];
        foreach ($tables as $table) {
            $return[$table] = $this->getFieldsInfo($table);
        }
        return $return;
    }

}//SchemaService
