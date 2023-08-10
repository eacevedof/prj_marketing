<?php

namespace App\Apify\Application\Security;

use Exception;
use App\Shared\Infrastructure\Services\AppService;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Session\ComponentEncDecrypt;

final class LoginMiddleService extends AppService
{
    private const POST_USER_KEY = "apify-user";
    private const POST_PASSWORD_KEY = "apify-password";

    private ?string $origin = null;
    private array $post = [];
    private ?ComponentEncDecrypt $componentEncdecrypt = null;

    public function __construct(array $post = [])
    {
        //el post con los datos de usuario
        $this->post = $post;
        //necesito el dominio pq la encriptación va por dominio en el encdecrypt.json
        $this->origin = $this->post["remotehost"] ?? "";
        $this->_loadComponentEncdecryptByConfigJson();
    }

    private function _getEncDecConfigFromJsonFile(): array
    {
        $jsonFile = $this->getEnvValue("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $configDecrypt = (new ComponentConfig($jsonFile))->get_node("domain", $this->origin);
        return $configDecrypt;
    }

    private function _loadComponentEncdecryptByConfigJson(): void
    {
        $config = $this->_getEncDecConfigFromJsonFile();
        //$this->logd($config,"_load_encdec");
        if (!$config) {
            throw new \Exception("domain {$this->origin} is not authorized 2 by middle");
        }

        $this->componentEncdecrypt = new ComponentEncDecrypt(1);
        $this->componentEncdecrypt->setSslMethod($config["sslenc_method"] ?? "");
        $this->componentEncdecrypt->setSslKey($config["sslenc_key"] ?? "");
        $this->componentEncdecrypt->setSaltString($config["sslsalt"] ?? "");
    }

    private function _getLoginConfigByHostName(string $hostName = ""): array
    {
        if (!$hostName) {
            $hostName = $this->origin;
        }
        $pathJson = $_ENV["APP_LOGIN"] ?? __DIR__.DIRECTORY_SEPARATOR."pos-login.json";
        $config = (new ComponentConfig($pathJson))->get_node("domain", $hostName);
        return $config;
    }

    private function _getUserPasswordByHostNameAndUserName(string $hostName, string $userName): string
    {
        $loginConfig = $this->_getLoginConfigByHostName($hostName);
        foreach($loginConfig["users"] as $arUserConf) {
            if ($arUserConf["user"] === $userName) {
                return $arUserConf[self::POST_PASSWORD_KEY] ?? "";
            }
        }

        return "";
    }

    private function _getRemoteIp(): string
    {
        return $this->post["remoteip"] ?? "";
    }

    private function _getUserAgent(): string
    {
        return $this->post["useragent"] ?? ":)";
    }

    private function _getFingerPrintTokenized(): string
    {
        $username = $this->post[self::POST_USER_KEY];
        $arPackage = [
            "salt0"    => date("Ymd-His"),
            "domain"   => $this->origin,
            "salt1"    => rand(0, 3),
            "remoteip" => $this->_getRemoteIp(),
            "salt2"    => rand(3, 7),
            "useragent" => md5($this->_getUserAgent()),
            "salt3"    => rand(7, 11),
            "username" => $username,
            "salt4"    => rand(11, 15),
            "password" => md5($this->_getUserPasswordByHostNameAndUserName($this->origin, $username)),
            "salt5"    => rand(15, 19),
            "today"    => date("Ymd-His"),
        ];

        $inString = implode("|", $arPackage);
        $this->logDebug($inString, "instring");
        $token = $this->componentEncdecrypt->getSslEncrypted($inString);
        return $token;
    }

    public function getAuthTokenOrFail(): string
    {
        if (!$this->origin) {
            throw new Exception("No origin domain provided");
        }

        $userName = $this->post[self::POST_USER_KEY] ?? "";
        if (!$userName) {
            throw new Exception("No user provided");
        }

        $password = $this->post[self::POST_PASSWORD_KEY] ?? "";
        if (!$password) {
            throw new Exception("No password provided");
        }

        $remoteIp = $this->post["remoteip"] ?? "";
        if (!$remoteIp) {
            throw new Exception("No remote ip provided");
        }

        $config = $this->_getLoginConfigByHostName();
        if (!$config) {
            throw new Exception("Source hostname not authorized");
        }

        $users = $config["users"] ?? [];
        foreach ($users as $user) {
            if(
                $user["apifyuser"] === $userName &&
                $this->componentEncdecrypt->isValidPassword($password, $user["apifypassword"])
            ) {
                return $this->_getFingerPrintTokenized();
            }
        }
        throw new Exception("Bad user or password");
    }
}
