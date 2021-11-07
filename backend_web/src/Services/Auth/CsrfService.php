<?php

namespace App\Services\Auth;
use App\Enums\Key;
use App\Services\AppService;
use TheFramework\Components\Formatter\ComponentMoment;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Traits\SessionTrait;

final class CsrfService extends AppService
{
    use SessionTrait;

    private ComponentEncdecrypt $encdec;
    private const VALID_TIME_IN_MINS = 20;

    public function __construct()
    {
        $this->encdec = $this->_get_encdec();
        $this->_sessioninit();
    }

    private function _get_domain(): string {return $_SERVER["REMOTE_HOST"]  ?? "";}

    private function _get_remote_ip(): string {return $_SERVER["REMOTE_ADDR"]  ?? "127.0.0.1";}

    private function _get_user_agent(): string {return $_SERVER["HTTP_USER_AGENT"] ?? ":)"; }

    public function get_token(): string
    {
        $user = $this->session->get(Key::AUTH_USER);

        $arpackage = [
            "salt0"    => date("Y-m-d H:i:s"),
            "domain"   => $this->_get_domain(),
            "salt1"    => rand(0,3),
            "remoteip" => $this->_get_remote_ip(),
            "salt2"    => rand(3,7),
            "useragent" => md5($this->_get_user_agent()),
            "salt3"    => rand(7,11),
            "username" => $user["email"] ?? "",
            "salt4"    => rand(11,15),
            "password" => md5($user["secret"] ?? ""),
            "salt5"    => rand(15,19),
            "today"    => date("Y-m-d H:i:s"),
        ];

        $instring = implode("|",$arpackage);
        $token = $this->encdec->get_sslencrypted($instring);
        return $token;
    }

    private function _validate_package($arpackage): void
    {
        if(count($arpackage)!==12) $this->_exeption(__("Invalid csrf {0}",1));;

        list($s0,$domain,$s1,$remoteip,$s2,$useragent,$s3,$username,$s4,$password,$s5,$date) = $arpackage;

        if($domain!==$this->domain) $this->_exeption(__("Invalid csrf {0}",2));;

        //hago validacion en local por peticiones entre las ips de docker y mi maquina host
        //que usan distitntas ips
        if ($remoteip !== $this->_get_remote_ip())
            $this->_exeption(__("Invalid csrf {0}",3));

        if($useragent !== md5($this->_get_user_agent())) $this->_exeption(__("Invalid csrf {0}",4));

        $user = $this->session->get(Key::AUTH_USER);
        $md5pass = $user["secret"] ?? "";
        $md5pass = md5($md5pass);
        if($md5pass!==$password) $this->_exeption(__("Invalid csrf {0}",5));

        $moment = new ComponentMoment($date);
        $now = date("Y-m-d H:i:s");
        $mins = (int) $moment->get_nmins($now);
        if($mins > self::VALID_TIME_IN_MINS) $this->_exeption(__("Expired csrf {0}",6));
    }

    public function is_valid(?string $token): bool
    {
        if(!$token) return false;

        $instring = $this->encdec->get_ssldecrypted($token);
        $arpackage = explode("|",$instring);
        $this->_validate_package($arpackage);
        return true;
    }
}