<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\Console\Restrict\UsersAccessService
 * @file UsersAccessService.php 1.0.0
 * @date 31-10-2020 17:46 SPAIN
 * @observations
 */
namespace App\Services\Console\Restrict;
use App\Services\AppService;
use App\Services\Console\IConsole;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Traits\ConsoleTrait;
use App\Factories\DbFactory as DbF;
use TheFramework\Components\Db\ComponentCrud;

final class UsersService extends AppService implements IConsole
{
    use ConsoleTrait;

    private ComponentEncdecrypt $encdec;
    private string $word;

    public function __construct(array $input)
    {
        $this->word = $input[0] ?? ":)";
        $this->encdec = $this->_get_encdec();
    }

    private function _get_password(): string
    {
        return $this->encdec->get_hashpassword($this->word);
    }

    private function _faker(): void
    {
        $db = DbF::get_by_default();
        $crud = new ComponentCrud();
        $crud->set_table("base_users");

        $db->exec();
    }

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