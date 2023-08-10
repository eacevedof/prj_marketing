<?php

namespace App\Restrict\Auth\Application;

use App\Shared\Infrastructure\Services\AppService;
use TheFramework\Components\Formatter\ComponentMoment;
use TheFramework\Components\Session\ComponentEncDecrypt;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;

final class CsrfService extends AppService
{
    private ?array $authUserArray;
    private ComponentEncDecrypt $componentEncdecrypt;
    private const VALID_TIME_IN_MINS = 180;

    public function __construct()
    {
        $this->componentEncdecrypt = $this->_getEncDecryptInstance();
        $this->authUserArray = SF::getAuthService()->getAuthUserArray();
    }

    private function _getDomain(): string
    {
        return $_SERVER["REMOTE_HOST"] ?? "";
    }

    private function _getRemoteIp(): string
    {
        return $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
    }

    private function _getUserAgent(): string
    {
        return $_SERVER["HTTP_USER_AGENT"] ?? ":)";
    }

    private function _isValidCsrfPackageOrFail(array $arPackage): void
    {
        if(count($arPackage) !== 12) {
            $this->_throwException(__("Invalid or expired CSRF. Reload this page please. c{0}", 1));
        }

        list($s0, $domain, $s1, $remoteip, $s2, $useragent, $s3, $username, $s4, $password, $s5, $date) = $arPackage;

        if ($domain !== $this->_getDomain()) {
            $this->_throwException(__("Invalid or expired CSRF. Reload this page please. c{0}", 2));
        }

        //hago validacion en local por peticiones entre las ips de docker y mi maquina host
        //que usan distitntas ips
        if ($remoteip !== $this->_getRemoteIp()) {
            $this->_throwException(__("Invalid or expired CSRF. Reload this page please. c{0}", 3));
        }

        if ($useragent !== md5($this->_getUserAgent())) {
            $this->_throwException(__("Invalid or expired CSRF. Reload this page please. c{0}", 4));
        }

        $md5pass = $this->authUserArray["secret"] ?? "";
        $md5pass = md5($md5pass);
        if ($md5pass !== $password) {
            $this->_throwException(__("Invalid or expired CSRF. Reload this page please. c{0}", 5));
        }

        $moment = new ComponentMoment($date);
        $now = date("Y-m-d H:i:s");
        $mins = (int) $moment->get_nmins($now);
        if ($mins > self::VALID_TIME_IN_MINS) {
            $this->_throwException(__("Expired CSRF {0}", 6));
        }
    }

    public function getCsrfToken(): string
    {
        $csrfPackage = [
            "salt0"    => date("Y-m-d H:i:s"),
            "domain"   => $this->_getDomain(),
            "salt1"    => rand(0, 3),
            "remoteip" => $this->_getRemoteIp(),
            "salt2"    => rand(3, 7),
            "useragent" => md5($this->_getUserAgent()),
            "salt3"    => rand(7, 11),
            "username" => $this->authUserArray["email"] ?? "",
            "salt4"    => rand(11, 15),
            "password" => md5($this->authUserArray["secret"] ?? ""),
            "salt5"    => rand(15, 19),
            "today"    => date("Y-m-d H:i:s"),
        ];

        $csrfToEncrypt = implode("|", $csrfPackage);
        $token = $this->componentEncdecrypt->getSslEncrypted($csrfToEncrypt);
        return $token;
    }

    public function isValidCsrfToken(?string $csrfToken): bool
    {
        if (!$csrfToken) {
            return false;
        }

        $csrfDecrypted = $this->componentEncdecrypt->getSslDecrypted($csrfToken);
        $csrfPackage = explode("|", $csrfDecrypted);
        $this->_isValidCsrfPackageOrFail($csrfPackage);
        return true;
    }
}
