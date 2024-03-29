<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\LoginController
 * @file LoginController.php 1.0.0
 * @date 03-06-2020 18:17 SPAIN
 * @observations
 */

namespace App\Controllers\Apify\Security;

use Exception;
use App\Shared\Domain\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Security\{LoginMiddleService, LoginService};

final class LoginController extends ApifyController
{
    /**
     * ruta:
     *  <dominio>/apifiy/security/pos-login
     */
    public function index()
    {
        $json = $this->_getJsonInstanceFromResponse();
        try {
            $domain = $this->get_domain(); //exception
            $oServ = new LoginService($domain, $this->requestComponent->getPost());
            $token = $oServ->get_token();
            $json->setPayload(["token" => $token])->show();
        } catch (Exception $e) {
            $this->logerr($e->getMessage(), "LoginController.index");
            $json->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([$e->getMessage()])
                ->show(1);
        }
    }

    /**
     * Para servidores intermediarios
     * El serv tiene que hacer un forward en POST de remoteip y remotehost
     * ruta:
     *  <dominio>/apifiy/security/pos-login-middle
     */
    public function middle()
    {
        $this->logd("middle start");
        $json = $this->_getJsonInstanceFromResponse();
        try {
            $oServ = new LoginMiddleService($this->requestComponent->getPost());
            $token = $oServ->get_token();
            $json->setPayload(["token" => $token])->show();
        } catch (Exception $e) {
            $this->logerr($e->getMessage(), "LoginController.middle");
            $json->setResponseCode(ResponseType::UNAUTHORIZED)
                ->setErrors([$e->getMessage()])
                ->show(1);
        }
        $this->logd("middle end");
    }

    /**
     * ruta:
     *  <dominio>/apifiy/security/is-valid-token
     */
    public function is_valid_token()
    {
        $json = new HelperJson;
        try {
            //$token = $this->get_header("apify-auth");
            //$token = $this->get_header("authorization");
            $domain = $this->get_domain(); //excepcion
            $this->logreq($domain, "pos-login.is_valid_token.domain");
            $token = $this->requestComponent->getPost(self::KEY_APIFYUSERTOKEN);
            $this->logreq($token, "pos-login.is_valid_token.post");
            $this->logreq("domain: $domain, token: $token");
            if (!$token) {
                throw new \Exception("No token provided");
            }
            $oServ = new LoginService($domain);
            $oServ->is_valid($token);
            $json->set_payload(["isvalid" => true])->show();
        } catch (Exception $e) {
            $this->logerr($e->getMessage(), "LoginController.is_valid_token");
            $json->set_code(HelperJson::CODE_FORBIDDEN)
                ->set_error([$e->getMessage()])
                ->show(1);
        }
    }
}//LoginController
