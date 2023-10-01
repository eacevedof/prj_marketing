<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Dev\Builder\TranslationService
 * @file TranslationService.php 1.0.0
 * @date 31-10-2022 17:46 SPAIN
 * @observations
 */

namespace App\Console\Application\Dev\Translation;

use BOOT;
use App\Console\Application\IConsole;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\ConsoleTrait;

final class TranslationService extends AppService implements IConsole
{
    use ConsoleTrait;

    private const FIND_TR_PATTERNS = [
        "\_\_\(\"(.*?)\"",
    ];

    private const PATH_SRC = BOOT::PATH_SRC;
    private const PATH_TR_ES = PATH_ROOT."/locale/es/default.po";

    private array $arFiles;
    private array $skipFolders;
    private array $translations;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->arFiles = [];
        $this->skipFolders = [
            "/appdata/www/backend_web/src/xxx-module",
            "/appdata/www/backend_web/src/Restrict/Users",
            //"/appdata/www/backend_web/src/Restrict/Promotions",
            "/appdata/www/backend_web/src/Shared/Infrastructure/Helpers/Views/DatatableHelper.php"
        ];
        $this->translations = [];
    }

    public function _getFiles(string $pathDir): array
    {
        $files = scandir($pathDir);
        if(count($files) < 3) {
            return [];
        }
        unset($files[0]);
        unset($files[1]);
        return array_map(
            function ($file) use ($pathDir) {
                return "$pathDir/$file";
            },
            array_values($files)
        );
    }

    private function _loadFilesRecursive(string $path): void
    {
        $files = $this->_getFiles($path);
        foreach ($files as $file) {
            if(in_array($file, $this->skipFolders)) {
                continue;
            }
            if (is_file($file)) {
                $this->arFiles[] = $file;
            }
            if (is_dir($file)) {
                $this->_loadFilesRecursive($file);
            }
        }
    }

    private function _getTranslations(string $content, string $pattern): array
    {
        //$pattern = self::FIND_TR_PATTERN;
        $pattern = "/$pattern/m";
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        $result = $matches[1] ?? [];
        return array_unique($result);
    }

    private function _addTranslations(array $trs): void
    {
        foreach ($trs as $tr) {
            $this->translations[] = $tr;
        }
    }

    private function _getMissingEsTranslations(): array
    {
        $estrs = file_get_contents(self::PATH_TR_ES);
        $missing = [];
        foreach ($this->translations as $tr) {
            if (strstr($estrs, $tr) || trim($tr) === "") {
                continue;
            }
            $missing[] = "msgid \"$tr\"";
            $missing[] = "msgstr \"$tr\"";
        }
        return $missing;
    }

    private function _getNotUsedEs(): array
    {
        $estrs = file_get_contents(self::PATH_TR_ES);
        $lines = explode("\n", $estrs);
        $lines = array_filter($lines, function ($line) {
            return strstr($line, "msgid \"");
        });

        $lines = array_map(function ($line) {
            $line = substr($line, 0, -1);
            return str_replace("msgid \"", "", $line);
        }, $lines);

        $missing = [];
        foreach ($lines as $i => $line) {
            if (!in_array($line, $this->translations)) {
                $missing[] = "$line ({$i})";
            }
        }

        return $missing;
    }

    private function _getRepeatedTranslations(): array
    {
        $estrs = file_get_contents(self::PATH_TR_ES);
        $lines = explode("\n", $estrs);
        $lines = array_filter($lines, function ($line) {
            return strstr($line, "msgid \"");
        });

        $lines = array_map(function ($line) {
            $line = substr($line, 0, -1);
            return str_replace("msgid \"", "", $line);
        }, $lines);

        $count = array_count_values($lines);
        $count = array_filter($count, function ($num) {
            return $num > 1;
        });
        $count = array_keys($count);

        $found = [];
        foreach ($lines as $i => $line) {
            if (in_array($line, $count)) {
                $found[] = "$line ({$i})";
            }
        }

        return $found;
    }

    private function _loadAllInvokedTranslations(): void
    {
        foreach ($this->arFiles as $path) {
            $content = file_get_contents($path);
            if (!strstr($content, "__(\"")) {
                continue;
            }
            foreach (self::FIND_TR_PATTERNS as $pattern) {
                $trs = $this->_getTranslations($content, $pattern);
                $this->_addTranslations($trs);
            }
        }
        $trs = array_values(array_unique($this->translations));
        $this->translations = $trs;
    }

    //php run.php modules
    //run get-translation
    public function run(): void
    {
        $this->_loadFilesRecursive(self::PATH_SRC);
        $this->_loadAllInvokedTranslations();

        $parameter = trim($this->input[0] ?? "");
        switch ($parameter) {
            case "--not-used":
                $found = $this->_getNotUsedEs();
                break;
            case "--repeated":
                $found = $this->_getRepeatedTranslations();
                break;
            default:
                $found = $this->_getMissingEsTranslations();
                break;
        }

        foreach ($found as $tr) {
            print_r($tr."\n");
        }
    }
}
