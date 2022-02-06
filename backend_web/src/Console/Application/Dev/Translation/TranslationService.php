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

    private const FIND_TR_PATTERN = "\_\_\(\"(.*)\"\)";
    private const PATH_SRC = PATH_SRC;
    private array $arfiles;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->arfiles = [];
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
            if (is_file($file)) $this->arfiles[] = $file;
            if (is_dir($file)) $this->_load_files($file);
        }
    }

    private function _get_trs(string $content): string
    {
        $pattern = self::FIND_TR_PATTERN;
        $pattern = "/$pattern/";

        $matches = [];
        preg_match_all($pattern, $content, $matches);
        return $matches[0][1] ?? [];
    }

    //php run.php modules
    //run get-translation
    public function run(): void
    {
        $this->_load_files(self::PATH_SRC);
        $this->logpr($this->arfiles,"files");
        foreach ($this->arfiles as $path) {
            $content = file_get_contents($path);
            if (!strstr($content, "__(\"")) continue;
            $trs = $this->_get_trs($content);
            $this->logpr($trs,"trs");
        }
    }
}