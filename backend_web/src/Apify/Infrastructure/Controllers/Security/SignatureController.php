<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\SignatureController
 * @file SignatureController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */

namespace App\Controllers\Apify\Security;

use App\Shared\Domain\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Security\SignatureService;

final class SignatureController extends ApifyController
{
    /**
     * ruta:
     *  <dominio>/apifiy/security/get-signature
     */
    public function index()
    {
        try {
            $domain = $this->get_domain(); //excepcion
            $oServ = new SignatureService($domain, $this->requestComponent->getPost());
            $token = $oServ->get_token();
            $this->_getJsonInstanceFromResponse()->setPayload(["result" => $token])->show();
        } catch (\Exception $e) {
            $this->logerr($e->getMessage(), "SignatureController.index");
            $this->_getJsonInstanceFromResponse()
                ->setResponseCode(ResponseType::INTERNAL_SERVER_ERROR)
                ->setErrors([$e->getMessage()])
                ->show();
        }

    }//index

    /**
     * ruta:
     *  <dominio>/apifiy/security/is-valid-signature
     */
    public function is_valid_signature(): void
    {
        $this->_check_signature();
        $this->_getJsonInstanceFromResponse()->setPayload(["result" => true])->show();
    }//index

}//SignatureController
