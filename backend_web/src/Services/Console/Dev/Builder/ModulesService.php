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

    private string $table;
    private SchemaService $schema;
    private const PATH_XXXMODULE = PATH_SRC."/xxx-module";
    private const PATH_FILESTPL = PATH_SRC."/xxx-module/files";
    private string $pathbuild = "";
    private array $filestpl = [];
    private array $builders = [];
    private array $fields = [];
    private array $aliases = [];

    public function __construct(array $input)
    {
        $this->table = $input[0] ?? "";
        $this->schema = new SchemaService(DF::get_by_default());
        $this->_check_input();
        $this->pathbuild = self::PATH_XXXMODULE . "/module-{$this->table}-".date("YmdHis");

        $this->_load_aliases();
        $this->_load_fields();
        $this->_load_tplfiles();
        $this->_load_builders();
    }

    private function _check_input(): void
    {
        if (!$this->table || !is_string($this->table))
            $this->_exception("valid required input is a tablename");
        $tables = $this->schema->get_tables();
        $tables = array_column($tables,"table_name");
        if (!in_array($this->table, $tables))
            $this->_exception("not valid table: {$this->table}. Valid are: ".implode(", ",$tables));
    }

    private function _tocamelcase(string $string): string
    {
        $str = str_replace(" ","", ucwords(str_replace("-", " ", $string)));
        return $str;
    }
    
    private function _load_aliases(): void
    {
        $this->aliases["noprefix"] = ($noprefix = str_replace(["app_","base_"],"",$this->table));
        $this->aliases["raw"] = $this->table;
        $this->aliases["uppercased"] = $this->_tocamelcase($noprefix);
        $this->aliases["uppercased-plural"] = $this->_tocamelcase($noprefix)."s";
    }

    private function _load_fields(): void
    {
        $this->fields = $this->schema->get_fields_info($this->table);
    }

    private function _load_tplfiles(): void
    {
        $files = scandir(self::PATH_FILESTPL);
        foreach ($files as $file)
            $this->filestpl[$file] = self::PATH_FILESTPL."/$file";
        unset($this->filestpl["."],$this->filestpl[".."]);
    }

    private function _load_builders(): void
    {
        $this->builders["entity"] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl["XxxEntity.php"], PhpBuilder::TYPE_ENTITY
        );
        $this->builders["repository"] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl["XxxRepository.php"], PhpBuilder::TYPE_REPOSITORY
        );
    }

    //php run.php modules <table-name> o
    //run modules <table-name> (en ssh-be)
    public function run(): void
    {
        mkdir($this->pathbuild);

        //$this->_pr($this->filestpl);
        //$fields = $this->schema->get_fields_info($this->table);
        //$this->_pr($fields);
    }
}