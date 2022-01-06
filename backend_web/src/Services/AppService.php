<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\AppService 
 * @file AppService.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Services;

use App\Traits\ErrorTrait;
use App\Traits\LogTrait;
use App\Traits\EnvTrait;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncdecrypt;
use \Exception;

/**
 * Class AppService
 * @package App\Services
 * No constructor,
 * ErrorTrait, LogTrait, EvnTrait, input,
 * _exception, _get_encdec
 */
abstract class AppService
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    protected $input;

    protected function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }

    protected function _get_encdec(): ComponentEncdecrypt
    {
        $pathfile = $this->get_env("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $config = (new ComponentConfig($pathfile))->get_node("domain", $this->get_env("APP_DOMAIN"));
        if(!$config) $this->_exception("Domain {$this->get_env("APP_DOMAIN")} is not authorized");

        $encdec = new ComponentEncdecrypt(1);
        $encdec->set_sslmethod($config["sslenc_method"] ?? "");
        $encdec->set_sslkey($config["sslenc_key"] ?? "");
        $encdec->set_sslsalt($config["sslsalt"] ?? "");
        return $encdec;
    }

}//AppService
