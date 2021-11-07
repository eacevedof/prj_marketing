<?php

namespace App\Services\Auth;
use App\Factories\SessionFactory as SF;
use App\Enums\Key;
use App\Services\AppService;
use TheFramework\Components\Config\ComponentConfig;
use TheFramework\Components\Formatter\ComponentMoment;
use TheFramework\Components\Session\ComponentEncdecrypt;
use App\Traits\SessionTrait;
use \Exception;

final class CsrfService extends AppService
{
    use SessionTrait;

    private ComponentEncdecrypt $encdec;

    public function __construct()
    {
        $this->encdec = $this->_get_encdec();
        $this->_sessioninit();
    }

    private function _get_domain(): string {return $_SERVER["REMOTE_HOST"]  ?? "";}

    private function _get_remote_ip(): string {return $_SERVER["REMOTE_ADDR"]  ?? "127.0.0.1";}

    private function _get_user_agent(): string {return $_SERVER["HTTP_USER_AGENT"] ?? ":)"; }

    private function get_token(): string
    {
        $user = $this->session->get(Key::AUTH_USER);

        $arpackage = [
            "salt0"    => date("Ymd-His"),
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
            "today"    => date("Ymd-His"),
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

        //hago validacion en local por peticiones entre las ips de docker y mi maquina host que usan distitntas ips
        if ($remoteip !== $this->_get_remote_ip()) $this->_exeption(__("Invalid csrf {0}",3));;


        if($useragent !== md5($this->_get_user_agent())) throw new Exception("Wrong user agent");

        $md5pass = $this->_get_user_password($domain, $username);
        $md5pass = md5($md5pass);
        if($md5pass!==$password) throw new Exception("Wrong user or password submitted");

        list($day) = explode("-",$date);
        $now = date("Ymd");
        $moment = new ComponentMoment($day);
        $ndays = $moment->get_ndays($now);
        if($ndays>30)
            throw new Exception("Token has expired");
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