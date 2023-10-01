<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Dev\Builder\ModuleBuilderService
 * @file ModuleBuilderService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */

namespace App\Console\Application\Dev\Builder;

use BOOT;
use App\Console\Application\IConsole;
use App\Dbs\Application\SchemaService;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\ConsoleTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DF;

final class ModuleBuilderService extends AppService implements IConsole
{
    use ConsoleTrait;

    private string $table;
    private SchemaService $schema;
    private const PATH_XXXMODULE = BOOT::PATH_SRC."/xxx-module";
    private const PATH_FILESTPL = BOOT::PATH_SRC."/xxx-module/files";
    private string $pathBuild = "";
    private array $filesTpl = [];
    private array $builders = [];
    private array $fields = [];
    private array $aliases = [];

    public function __construct(array $input)
    {
        $this->table = $input[0] ?? "";
        $this->schema = new SchemaService(DF::getMysqlInstanceByEnvConfiguration());
        $this->_check_input();
        $this->pathBuild = self::PATH_XXXMODULE . "/module-{$this->table}-".date("YmdHis");

        $this->_load_aliases();
        $this->_load_fields();
        $this->_load_tplfiles();

        $this->_loadPhpDomain();
        $this->_loadPhpController();
        $this->_loadPhpServices();
        $this->_loadFrontBuilders();
        $this->_loadExtraBuilders();
    }

    private function _check_input(): void
    {
        if (!$this->table || !is_string($this->table)) {
            $this->_throwException("valid required input is a tablename");
        }
        $tables = $this->schema->getTables();
        $tables = array_column($tables, "table_name");
        if (!in_array($this->table, $tables)) {
            $this->_throwException("not valid table: {$this->table}. Valid are: ".implode(", ", $tables));
        }
    }

    private function _tocamelcase(string $string): string
    {
        $str = str_replace(" ", "", ucwords(str_replace("-", " ", $string)));
        return $str;
    }

    private function _load_aliases(): void
    {
        $this->aliases["noprefix"] = ($noprefix = str_replace(["app_","base_"], "", $this->table));
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
        $this->fields = $this->schema->getFieldsInfo($this->table);
    }

    private function _load_tplfiles(): void
    {
        $files = scandir(self::PATH_FILESTPL);
        if (($key = array_search(".", $files)) !== false) {
            unset($files[$key]);
        }
        if (($key = array_search("..", $files)) !== false) {
            unset($files[$key]);
        }


        foreach ($files as $dir) {
            if ($dir === "extra.md") {
                $this->filesTpl[$dir] = self::PATH_FILESTPL ."/$dir";
                continue;
            }

            $scanned = scandir(self::PATH_FILESTPL . "/$dir");
            if (($key = array_search(".", $scanned)) !== false) {
                unset($scanned[$key]);
            }
            if (($key = array_search("..", $scanned)) !== false) {
                unset($scanned[$key]);
            }

            foreach ($scanned as $file) {
                $this->filesTpl["$dir/$file"] = self::PATH_FILESTPL . "/$dir/$file";
            }
        }
    }

    private function _loadPhpDomain(): void
    {
        $this->builders[PhpBuilder::TYPE_ENTITY] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_ENTITY],
            $this->pathBuild,
            PhpBuilder::TYPE_ENTITY
        );

        $this->builders[PhpBuilder::TYPE_REPOSITORY] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_REPOSITORY],
            $this->pathBuild,
            PhpBuilder::TYPE_REPOSITORY
        );
    }

    private function _loadPhpController(): void
    {
        $this->builders[PhpBuilder::TYPE_DELETE_CONTROLLER] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_DELETE_CONTROLLER],
            $this->pathBuild,
            PhpBuilder::TYPE_DELETE_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_INFO_CONTROLLER] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_INFO_CONTROLLER],
            $this->pathBuild,
            PhpBuilder::TYPE_INFO_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_INSERT_CONTROLLER] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_INSERT_CONTROLLER],
            $this->pathBuild,
            PhpBuilder::TYPE_INSERT_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_SEARCH_CONTROLLER] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_SEARCH_CONTROLLER],
            $this->pathBuild,
            PhpBuilder::TYPE_SEARCH_CONTROLLER
        );
        $this->builders[PhpBuilder::TYPE_UPDATE_CONTROLLER] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_UPDATE_CONTROLLER],
            $this->pathBuild,
            PhpBuilder::TYPE_UPDATE_CONTROLLER
        );
    }

    private function _loadPhpServices(): void
    {
        $this->builders[PhpBuilder::TYPE_DELETE_SERVICE] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_DELETE_SERVICE],
            $this->pathBuild,
            PhpBuilder::TYPE_DELETE_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_INFO_SERVICE] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_INFO_SERVICE],
            $this->pathBuild,
            PhpBuilder::TYPE_INFO_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_INSERT_SERVICE] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_INSERT_SERVICE],
            $this->pathBuild,
            PhpBuilder::TYPE_INSERT_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_SEARCH_SERVICE] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_SEARCH_SERVICE],
            $this->pathBuild,
            PhpBuilder::TYPE_SEARCH_SERVICE
        );
        $this->builders[PhpBuilder::TYPE_UPDATE_SERVICE] = new PhpBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[PhpBuilder::TYPE_UPDATE_SERVICE],
            $this->pathBuild,
            PhpBuilder::TYPE_UPDATE_SERVICE
        );
    }

    private function _loadFrontBuilders(): void
    {
        $this->builders[FrontBuilder::TYPE_INDEX_TPL] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_INDEX_TPL],
            $this->pathBuild,
            FrontBuilder::TYPE_INDEX_TPL
        );

        $this->builders[FrontBuilder::TYPE_INSERT_JS] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_INSERT_JS],
            $this->pathBuild,
            FrontBuilder::TYPE_INSERT_JS
        );
        $this->builders[FrontBuilder::TYPE_INSERT_TPL] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_INSERT_TPL],
            $this->pathBuild,
            FrontBuilder::TYPE_INSERT_TPL
        );

        $this->builders[FrontBuilder::TYPE_UPDATE_JS] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_UPDATE_JS],
            $this->pathBuild,
            FrontBuilder::TYPE_UPDATE_JS
        );
        $this->builders[FrontBuilder::TYPE_UPDATE_TPL] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_UPDATE_TPL],
            $this->pathBuild,
            FrontBuilder::TYPE_UPDATE_TPL
        );

        $this->builders[FrontBuilder::TYPE_INFO_TPL] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_INFO_TPL],
            $this->pathBuild,
            FrontBuilder::TYPE_INFO_TPL
        );

        $this->builders[FrontBuilder::TYPE_CSS] = new FrontBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl[FrontBuilder::TYPE_CSS],
            $this->pathBuild,
            FrontBuilder::TYPE_CSS
        );
    }

    private function _loadExtraBuilders(): void
    {
        $this->builders["extra.md"] = new ExtraBuilder(
            $this->aliases,
            $this->fields,
            $this->filesTpl["extra.md"],
            $this->pathBuild,
            ExtraBuilder::TYPE_EXTRA_MD
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
        mkdir($this->pathBuild);
        $this->_build();
    }
}
