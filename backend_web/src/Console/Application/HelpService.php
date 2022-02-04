<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Console\Application\Restrict\UsersService
 * @file UsersAccessService.php 1.0.0
 * @date 31-10-2020 17:46 SPAIN
 * @observations
 */
namespace App\Console\Application;
use App\Shared\Infrastructure\Services\AppService;
use App\Shared\Infrastructure\Traits\ConsoleTrait;

final class HelpService extends AppService implements IConsole
{
    use ConsoleTrait;

    private array $commands;

    public function __construct(array $input)
    {
        $this->input = $input;
        $pathservices = PATH_CONSOLE."/services.php";
        $this->commands = include($pathservices);
    }

    public function run(): void
    {
        $lines = [];
        $i = 1;
        foreach($this->commands as $alias => $service) {
            $lines[] = "\n($i) $alias:\n\t$service";
            $i++;
        }
        $message = implode("", $lines)."\n";
        $this->_pr($message,"help menu");
    }
}