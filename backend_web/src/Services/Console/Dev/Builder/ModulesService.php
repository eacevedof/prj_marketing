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
use App\Services\Dbs\SchemaService;
use App\Traits\ConsoleTrait;
use App\Factories\DbFactory as DF;

final class ModulesService extends AppService implements IConsole
{
    use ConsoleTrait;
    private SchemaService $schema;

    public function __construct(array $input)
    {
        $this->input = $input;
        if (!$this->input || !is_string($input))
            $this->_exception("valid required input is a tablename");
        $this->schema = new SchemaService(DF::get_by_default());
    }

    //php run.php modules <table-name> o
    //run modules <table-name> (en ssh-be)
    public function run(): void
    {
        $this->_pr($this->input,"input");
    }
}