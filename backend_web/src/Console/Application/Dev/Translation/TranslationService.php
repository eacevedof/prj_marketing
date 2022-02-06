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

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    //php run.php modules
    //run get-translation
    public function run(): void
    {
        //$this->logpr("itranl");
    }
}