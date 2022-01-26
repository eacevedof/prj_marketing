<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Dev\Builder\ModuleBuilderService
 * @file ModuleBuilderService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Dev\Builder;

use App\Services\Console\IConsole;
use App\Services\AppService;
use App\Traits\ConsoleTrait;
use App\Factories\DbFactory as DF;
use App\Services\Dbs\SchemaService;

final class ModuleBuilderService extends AppService implements IConsole
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

        $this->_load_phpdomain();
        $this->_load_phpcontroller();
        $this->_load_phpservices();
        $this->_load_frontbuilders();
        $this->_load_extrabuilders();
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
        $CamelCased = $this->_tocamelcase($noprefix);
        $this->aliases["raw"] = $this->table;
        $this->aliases["uppercased"] = $CamelCased;
        $this->aliases["uppercased-plural"] = "{$CamelCased}s";
        $this->aliases["uppered-plural"] = strtoupper($CamelCased)."S";
        $this->aliases["lowered"] = strtolower($CamelCased);
        $this->aliases["lowered-plural"] = strtolower($CamelCased)."s";
    }

    private function _load_fields(): void
    {
        $this->fields = $this->schema->get_fields_info($this->table);
    }

    private function _load_tplfiles(): void
    {
        $files = scandir(self::PATH_FILESTPL);
        if (($key = array_search(".", $files)) !== false) unset($files[$key]);
        if (($key = array_search("..", $files)) !== false) unset($files[$key]);


        foreach ($files as $dir) {
            if ($dir==="extra.md") {
                $this->filestpl[] = self::PATH_FILESTPL ."/$dir";
                continue;
            }

            $scanned = scandir(self::PATH_FILESTPL . "/$dir");
            if (($key = array_search(".", $scanned)) !== false) unset($scanned[$key]);
            if (($key = array_search("..", $scanned)) !== false) unset($scanned[$key]);

            foreach ($scanned as $file)
                $this->filestpl[] = self::PATH_FILESTPL . "/$dir/$file";
        }
    }

    private function _load_phpdomain(): void
    {
        $this->builders[PhpBuilder::TYPE_ENTITY] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_ENTITY], $this->pathbuild,PhpBuilder::TYPE_ENTITY
        );
        $this->builders[PhpBuilder::TYPE_REPOSITORY] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_REPOSITORY], $this->pathbuild,PhpBuilder::TYPE_REPOSITORY
        );
    }

    private function _load_phpcontroller(): void
    {
        $this->builders[PhpBuilder::TYPE_DELETE_CONTROLLER] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_DELETE_CONTROLLER], $this->pathbuild,PhpBuilder::TYPE_DELETE_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_INFO_CONTROLLER] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_INFO_CONTROLLER], $this->pathbuild,PhpBuilder::TYPE_INFO_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_INSERT_CONTROLLER] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_INSERT_CONTROLLER], $this->pathbuild,PhpBuilder::TYPE_INSERT_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_SEARCH_CONTROLLER] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_SEARCH_CONTROLLER], $this->pathbuild,PhpBuilder::TYPE_SEARCH_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_UPDATE_CONTROLLER] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_UPDATE_CONTROLLER], $this->pathbuild,PhpBuilder::TYPE_UPDATE_CONTROLLER
        );
    }

    private function _load_phpservices(): void
    {
        $this->builders[PhpBuilder::TYPE_DELETE_SERVICE] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_DELETE_SERVICE], $this->pathbuild,PhpBuilder::TYPE_DELETE_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_INFO_SERVICE] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_INFO_SERVICE], $this->pathbuild,PhpBuilder::TYPE_INFO_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_INSERT_SERVICE] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_INSERT_SERVICE], $this->pathbuild,PhpBuilder::TYPE_INSERT_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_SEARCH_SERVICE] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_SEARCH_SERVICE], $this->pathbuild,PhpBuilder::TYPE_SEARCH_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_UPDATE_SERVICE] = new PhpBuilder(
            $this->aliases, $this->fields, $this->filestpl[PhpBuilder::TYPE_UPDATE_SERVICE], $this->pathbuild,PhpBuilder::TYPE_UPDATE_SERVICE
        );
    }

    private function _load_frontbuilders(): void
    {
        $this->builders[FrontBuilder::TYPE_INDEX_TPL] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_INDEX_TPL], $this->pathbuild,FrontBuilder::TYPE_INDEX_TPL
        );

        $this->builders[FrontBuilder::TYPE_INSERT_JS] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_INSERT_JS], $this->pathbuild,FrontBuilder::TYPE_INSERT_JS
        );
        $this->builders[FrontBuilder::TYPE_INSERT_TPL] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_INSERT_TPL], $this->pathbuild,FrontBuilder::TYPE_INSERT_TPL
        );

        $this->builders[FrontBuilder::TYPE_UPDATE_JS] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_UPDATE_JS], $this->pathbuild,FrontBuilder::TYPE_UPDATE_JS
        );
        $this->builders[FrontBuilder::TYPE_UPDATE_TPL] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_UPDATE_TPL], $this->pathbuild,FrontBuilder::TYPE_UPDATE_TPL
        );

        $this->builders[FrontBuilder::TYPE_INFO_TPL] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_INFO_TPL], $this->pathbuild,FrontBuilder::TYPE_INFO_TPL
        );
        
        $this->builders[FrontBuilder::TYPE_CSS] = new FrontBuilder(
            $this->aliases, $this->fields, $this->filestpl[FrontBuilder::TYPE_CSS], $this->pathbuild,FrontBuilder::TYPE_CSS
        );
    }

    private function _load_extrabuilders(): void
    {
        $this->builders["extra.md"] = new ExtraBuilder(
            $this->aliases, $this->fields, $this->filestpl["extra.md"], $this->pathbuild,ExtraBuilder::TYPE_EXTRA_MD
        );
    }

    private function _build(): void
    {
        foreach ($this->builders as $alias => $builder) {
            $this->_pr("builder $alias running ...");
            $builder->build();
        }
    }

    //php run.php modules <table-name> o
    //run build-module app_promotion (en ssh-be)
    public function run(): void
    {
        mkdir($this->pathbuild);
        $this->_build();
    }
}