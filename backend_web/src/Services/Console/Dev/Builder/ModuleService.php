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

final class ModuleService extends AppService implements IConsole
{
    use ConsoleTrait;

    //php run.php users 1234
    public function run(): void
    {
        $this->_faker();
        $this->_pr($this->word,"word");
        $password = $this->_get_password();
        $message = "password: {$password}";
        $this->logpr($message);
    }
}