<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Restrict\UsersService
 * @file UsersService.php 1.0.0
 * @date 31-10-2020 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Restrict;
use App\Services\Console\IConsole;
use App\Traits\LogTrait;


final class UsersService implements IConsole
{
    use LogTrait;
    private array $input;

    public function __construct(array $input=[])
    {
        $this->input = $input;
    }

    public function run(): void
    {
        print_r($this->input);
    }
}