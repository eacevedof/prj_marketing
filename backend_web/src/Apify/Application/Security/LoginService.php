<?php

namespace App\Apify\Application\Security;

use Exception;
use App\Shared\Infrastructure\Services\AppService;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Formatter\ComponentMoment;
use TheFramework\Components\Session\ComponentEncDecrypt;

final class LoginService extends AppService
{
    private const POST_USER_KEY = "apify-user";
    private const POST_PASSWORD_KEY = "apify-password";

    private ?string $domain = null;
    private ?array $loginArray = null;

    private ?ComponentEncDecrypt $componentEncDecrypt = null;

    public function __construct(string $domain, array $loginArray = [])
    {
        //necesito el dominio pq la encriptación va por dominio en el encdecrypt.json
        $this->domain = $domain;
        //el post con los datos de usuario
        $this->loginArray = $loginArray;
        $this->_loadEncDecryptComponentFromJsonOrFail();
    }

    private function _loadEncDecryptComponentFromJsonOrFail(): void
    {
        if (!$config = $this->_getEncDecConfigFromJsonFile()) {
            throw new Exception("Domain {$this->domain} is not authorized 2");
        }

        $this->componentEncDecrypt = new ComponentEncDecrypt;
        $this->componentEncDecrypt->setSslMethod($config["sslenc_method"] ?? "");
        $this->componentEncDecrypt->setSslKey($config["sslenc_key"] ?? "");
        $this->componentEncDecrypt->setSaltString($config["sslsalt"] ?? "");
    }

    private function _getEncDecConfigFromJsonFile(): array
    {
        $pathFile = $this->getEnvValue("APP_ENCDECRYPT") ?? __DIR__.DIRECTORY_SEPARATOR."encdecrypt.json";
        $arconf = (new ComponentConfig($pathFile))->get_node("domain", $this->domain);
        return $arconf;
    }

    private function _getLoginConfigArray(string $domain = ""): array
    {
        if (!$domain) {
            $domain = $this->domain;
        }
        $pathLoginJson = $_ENV["APP_LOGIN"] ?? __DIR__.DIRECTORY_SEPARATOR."pos-login.json";
        $arconfig = (new ComponentConfig($pathLoginJson))->get_node("domain", $domain);
        return $arconfig;
    }

    private function _getUserPasswordInDomainByUsername(string $domain, string $username): string
    {
        $loginConfig = $this->_getLoginConfigArray($domain);
        foreach($loginConfig["users"] as $userConfig) {
            if ($userConfig[self::POST_USER_KEY] === $username) {
                return $userConfig[self::POST_PASSWORD_KEY] ?? "";
            }
        }
        return "";
    }

    private function _getRemoteIp(): string
    {
        return $_SERVER["REMOTE_ADDR"]  ?? "127.0.0.1";
    }

    private function _getHttpUserAgent(): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? ":)";
    }

    private function _getDataTokenized(): string
    {
        $username = $this->loginArray[self::POST_USER_KEY] ?? "";
        $infoPackage = [
            "salt0"    => date("Ymd-His"),
            "domain"   => $this->domain,
            "salt1"    => rand(0, 3),
            "remoteip" => $this->_getRemoteIp(),
            "salt2"    => rand(3, 7),
            "useragent" => md5($this->_getHttpUserAgent()),
            "salt3"    => rand(7, 11),
            "username" => $username,
            "salt4"    => rand(11, 15),
            "password" => md5($this->_getUserPasswordInDomainByUsername($this->domain, $username)),
            "salt5"    => rand(15, 19),
            "today"    => date("Ymd-His"),
        ];

        $pipedString = implode("|", $infoPackage);
        return $this->componentEncDecrypt->getSslEncrypted($pipedString);
    }

    public function getTokenFromLoginOrFail(): string
    {
        $username = $this->loginArray[self::POST_USER_KEY] ?? "";
        if (!$username) {
            throw new Exception("No user provided");
        }

        $password = $this->loginArray[self::POST_PASSWORD_KEY] ?? "";
        if (!$password) {
            throw new Exception("No password provided");
        }

        $config = $this->_getLoginConfigArray();
        if (!$config) {
            throw new Exception("Source domain not authorized");
        }

        $users = $config["users"] ?? [];
        foreach ($users as $user) {
            if ($user["apifyuser"] === $username && $this->componentEncDecrypt->isValidPassword($password, $user["apifypassword"])) {
                return $this->_getDataTokenized();
            }
        }
        throw new Exception("Bad user or password");
    }

    private function _checkLoginPackageOrFail(array $infoPackage): void
    {
        if(count($infoPackage) !== 12) {
            throw new Exception("Wrong token submitted (pieces)");
        }

        list($s0, $domain, $s1, $remoteip, $s2, $useragent, $s3, $username, $s4, $password, $s5, $date) = $infoPackage;

        if ($domain !== $this->domain) {
            throw new Exception("Domain {$this->domain} is not authorized 1");
        }

        //hago validacion en local por peticiones entre las ips de docker y mi maquina host que usan distitntas ips
        if (!$this->isEnvLocal() && $remoteip !== $this->_getRemoteIp()) {
            throw new Exception("Wrong source {$remoteip} in token");
        }

        if ($useragent !== md5($this->_getHttpUserAgent())) {
            throw new Exception("Wrong user agent");
        }

        $md5pass = $this->_getUserPasswordInDomainByUsername($domain, $username);
        $md5pass = md5($md5pass);
        if ($md5pass !== $password) {
            throw new Exception("Wrong user or password submitted");
        }

        list($day) = explode("-", $date);
        $now = date("Ymd");
        $moment = new ComponentMoment($day);
        $ndays = $moment->get_ndays($now);
        if ($ndays > 30) {
            throw new Exception("Token has expired");
        }
    }

    public function isValidTokenOrFail(?string $loginToken): bool
    {
        if (!$loginToken) {
            return false;
        }

        $loginStringDecrypted = $this->componentEncDecrypt->getSslDecrypted($loginToken);
        $loginPackage = explode("|", $loginStringDecrypted);
        $this->_checkLoginPackageOrFail($loginPackage);
        return true;
    }
}
