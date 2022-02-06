<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\PasswordController 
 * @file PasswordController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Apify\Security;

use App\Shared\Domain\Enums\ResponseType;
use App\Controllers\Apify\ApifyController;
use App\Services\Apify\Security\SignatureService;

final class PasswordController extends ApifyController
{

    /**
     * ruta:
     *  <dominio>/apifiy/security/get-password
     */
    public function index()
    {
        $json = new HelperJson();
        try{
            $domain = $this->get_domain(); //excepcion
            //prd($domain);
            $oServ = new SignatureService($domain, $this->request->get_post());
            $token = $oServ->get_password();
            $json->set_payload(["result"=>$token])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"PasswordController.index");
            $json->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show(1);
        }

    }//index


    
}//PasswordController
