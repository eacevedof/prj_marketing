<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Services\AppService
 * @file AppService.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Infrastructure\Services;

use App\Shared\Infrastructure\Traits\ErrorTrait;
use App\Shared\Infrastructure\Traits\LogTrait;
use App\Shared\Infrastructure\Traits\EnvTrait;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Shared\Infrastructure\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\BadRequestException;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
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

    /**
     * @param string $message
     * @param int $code
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    protected function _exception(string $message, int $code=ExceptionType::CODE_INTERNAL_SERVER_ERROR): void
    {
        $this->logerr($message,"app-service.exception");
        switch ($code) {
            case ExceptionType::CODE_BAD_REQUEST: throw new BadRequestException($message);
            case ExceptionType::CODE_FORBIDDEN: throw new ForbiddenException($message);
            case ExceptionType::CODE_NOT_FOUND: throw new NotFoundException($message);
        }
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
