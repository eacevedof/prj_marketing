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

use App\Enums\ResponseType;
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
        try{
            $domain = $this->get_domain(); //excepcion
            $oServ = new SignatureService($domain, $this->request->get_post());
            $token = $oServ->get_token();
            $this->_get_json()->set_payload(["result"=>$token])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"SignatureController.index");
            $this->_get_json()
                ->set_code(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_error([$e->getMessage()])
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
        $this->_get_json()->set_payload(["result"=>true])->show();
    }//index
    
}//SignatureController
