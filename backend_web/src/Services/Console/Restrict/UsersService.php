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
use App\Services\AppService;
use App\Services\Console\IConsole;
use TheFramework\Components\Session\ComponentEncdecrypt;

final class UsersService extends AppService implements IConsole
{
    private array $input;
    private ComponentEncdecrypt $encdec;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->encdec = $this->_get_encdec();
    }

    private function _get_password(): string
    {
        $word = $this->input[0] ?? ":)";
        return $this->encdec->get_hashpassword($word);
    }

    //php run.php users 1234
    public function run(): void
    {
        echo $this->input[0];
        $password = $this->_get_password();
        $message = "password: {$password}";
        $this->logpr($message);
    }
}