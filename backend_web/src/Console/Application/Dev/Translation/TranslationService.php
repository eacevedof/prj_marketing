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

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function _get_files(string $pathdir): array
    {
        $files = scandir($pathdir);
        if(count($files)<3) return [];
        unset($files[0]); unset($files[1]);
        return array_values($files);
    }

    //php run.php modules
    //run get-translation
    public function run(): void
    {
        $pathdir = self::PATH_SRC;
        //$this->logpr("itranl");
        $files = $this->_get_files($pathdir);
        $this->logpr($files,"files");
    }
}