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
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Traits\LogTrait;
use App\Traits\EnvTrait;

final class UsersService implements IConsole
{
    use LogTrait;
    use EnvTrait;
    private $domain;
    private $input;
    /**
     * @var ComponentEncdecrypt
     */
    private $encdec = null;

    public function __construct(array $input)
    {
        $this->domain = "localhost:900";
        $this->input = $input;
        $this->_load_encdec();
    }

    private function _get_encdec_config(): array
    {
        $pathfile = $this->get_env("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $arconf = (new ComponentConfig($pathfile))->get_node("domain",$this->domain);
        return $arconf;
    }

    private function _load_encdec(): void
    {
        $config = $this->_get_encdec_config($this->domain);
        if(!$config) throw new \Exception("Domain {$this->domain} is not authorized");

        $this->encdec = new ComponentEncdecrypt(1);
        $this->encdec->set_sslmethod($config["sslenc_method"]??"");
        $this->encdec->set_sslkey($config["sslenc_key"]??"");
        $this->encdec->set_sslsalt($config["sslsalt"]??"");
    }

    private function _get_password(): string
    {
        $word = $this->input[1] ?? ":)";
        $password = $this->encdec->get_hashpassword($word);
        return $password;
    }

    public function run(): void
    {
        $password = $this->_get_password();
        $message = "word: {$this->input[1]}, password: {$password}";
        $this->logpr($message, "password");
    }
}