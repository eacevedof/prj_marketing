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

use App\Console\Application\IConsole;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\ConsoleTrait;

final class TranslationService extends AppService implements IConsole
{
    use ConsoleTrait;

    private const FIND_TR_PATTERNS = [
        "\_\_\(\"(.*?)\"",
    ];
    private const PATH_SRC = PATH_SRC;
    private const PATH_TR_ES = PATH_ROOT."/locale/es/default.po";

    private array $arfiles;
    private array $skipfolders;
    private array $trs;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->arfiles = [];
        $this->skipfolders = [
            "/appdata/www/backend_web/src/xxx-module",
            //"/appdata/www/backend_web/src/Restrict/Users",
            "/appdata/www/backend_web/src/Restrict/Promotions",
        ];
        $this->trs = [];
    }

    public function _get_files(string $pathdir): array
    {
        $files = scandir($pathdir);
        if(count($files)<3) return [];
        unset($files[0]); unset($files[1]);
        return array_map(
            function ($file) use ($pathdir) {
                return "$pathdir/$file";
            },
            array_values($files)
        );
    }

    private function _load_files(string $path): void
    {
        $files = $this->_get_files($path);
        foreach ($files as $file) {
            if(in_array($file, $this->skipfolders)) continue;
            if (is_file($file)) $this->arfiles[] = $file;
            if (is_dir($file)) $this->_load_files($file);
        }
    }

    private function _get_trs(string $content, string $pattern): array
    {
        //$pattern = self::FIND_TR_PATTERN;
        $pattern = "/$pattern/m";
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        $result = $matches[1] ?? [];
        return array_unique($result);
    }

    private function _add_trs(array $trs): void
    {
        foreach ($trs as $tr) $this->trs[] = $tr;
    }

    private function _get_missing_es(array $trs): array
    {
        $estrs = file_get_contents(self::PATH_TR_ES);
        $missing = [];
        foreach ($trs as $tr) {
            if (strstr($estrs, $tr) || trim($tr)==="") continue;
            $missing[] = "msgid \"$tr\"";
            $missing[] = "msgstr \"$tr\"";
        }
        return $missing;
    }

    //php run.php modules
    //run get-translation
    public function run(): void
    {
        $this->_load_files(self::PATH_SRC);
        //$this->logpr($this->arfiles,"files");
        foreach ($this->arfiles as $path) {
            $content = file_get_contents($path);
            if (!strstr($content, "__(\"")) continue;
            foreach (self::FIND_TR_PATTERNS as $pattern) {
                $trs = $this->_get_trs($content, $pattern);
                $this->_add_trs($trs);
            }
        }
        $trs = array_values(array_unique($this->trs));
        $missing = $this->_get_missing_es($trs);
        foreach ($missing as $missing)
            print_r($missing."\n");
    }
}