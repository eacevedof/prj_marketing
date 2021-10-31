<?php
namespace App\Services\Restrict;
use App\Services\AppService;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Db\ComponentMysql;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Factories\DbFactory as DB;
use App\Traits\SessionTrait;
use \Exception;

final class LoginService extends AppService
{
    use SessionTrait;
    private string $domain;
    private array $input;
    private ComponentEncdecrypt $encdec;
    private ComponentMysql $db;

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->encdec = $this->_get_encdec();
        $this->db = $this->_get_db();
    }

    private function _get_user_by_user_and_password():array
    {
        $sql = "SELECT * FROM app_";
    }

    public function access(): void
    {

    }
}