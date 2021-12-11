<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Services\AppService 
 * @file AppService.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Services;

use App\Components\Auth\AuthComponent;
use App\Factories\ComponentFactory as CF;
use App\Factories\DbFactory;
use App\Traits\ErrorTrait;
use App\Traits\LogTrait;
use App\Traits\EnvTrait;
use \Exception;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Db\ComponentMysql;
use TheFramework\Components\Db\Context\ComponentContext;
use TheFramework\Components\Session\ComponentEncdecrypt;

abstract class AppService
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    private static ?AuthComponent $auth = null;

    protected function _get_auth(): ?AuthComponent
    {
        if (!self::$auth) self::$auth = CF::get("Auth/Auth");
        return self::$auth;
    }

    protected function _exeption(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }

    protected function _get_db(): ?ComponentMysql
    {
        $context = new ComponentContext($this->get_env("APP_CONTEXTS"), $this->get_env("APP_ID_CONTEXT"));
        $dbname = $context->get_dbname($this->get_env("APP_DB_ALIAS_1"));
        $db = DbFactory::get_dbobject_by_ctx($context, $dbname);
        if($db->is_error()) return $this->add_error($db->get_errors());
        return $db;
    }

    protected function _get_encdec(): ComponentEncdecrypt
    {
        $pathfile = $this->get_env("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $config = (new ComponentConfig($pathfile))->get_node("domain",$this->get_env("APP_DOMAIN"));
        if(!$config) $this->_exeption("Domain {$this->get_env("APP_DOMAIN")} is not authorized");

        $encdec = new ComponentEncdecrypt(1);
        $encdec->set_sslmethod($config["sslenc_method"]??"");
        $encdec->set_sslkey($config["sslenc_key"]??"");
        $encdec->set_sslsalt($config["sslsalt"]??"");
        return $encdec;
    }

    public function trim(&$arPost)
    {
        foreach($arPost as $sKey=>$sValue)
            $arPost[$sKey] = trim($sValue);
    }

}//AppService
