<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Dev\Builder\ModuleService
 * @file ModuleService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Dev\Builder;

use App\Services\Console\IConsole;
use App\Services\AppService;
use App\Traits\ConsoleTrait;
use App\Factories\DbFactory as DF;
use App\Services\Dbs\SchemaService;

final class ModulesService extends AppService implements IConsole
{
    use ConsoleTrait;
    private SchemaService $schema;
    private const PATH_XXXMODULE = PATH_SRC."/xxx-module";
    private const PATH_FILESTPL = PATH_SRC."/xxx-module/files";
    private string $pathbuild = "";

    public function __construct(array $input)
    {
        $this->input = $input[0] ?? "";
        $this->schema = new SchemaService(DF::get_by_default());
        $this->_check_input();
        $this->pathbuild = self::PATH_XXXMODULE . "/module-".date("YmdHis");
    }

    private function _check_input(): void
    {
        if (!$this->input || !is_string($this->input))
            $this->_exception("valid required input is a tablename");
        $tables = $this->schema->get_tables();
        $tables = array_column($tables,"table_name");
        if (!in_array($this->input, $tables))
            $this->_exception("not valid table: {$this->input}. Valid are: ".implode(", ",$tables));
    }



    //php run.php modules <table-name> o
    //run modules <table-name> (en ssh-be)
    public function run(): void
    {
        mkdir($this->pathbuild);
        $fields = $this->schema->get_fields_info($this->input);
        $this->_pr($fields);
    }
}