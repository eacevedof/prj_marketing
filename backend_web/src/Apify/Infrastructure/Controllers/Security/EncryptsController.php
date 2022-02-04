<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Apify\EncryptsController
 * @file Encrypt.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Apify\Security;

use App\Controllers\Apify\ApifyController;
use App\Shared\Infrastructure\Factories\ServiceFactory;

final class EncryptsController extends ApifyController
{
    /**
     * ruta: <dominio>/apify/encrypts
     */
    public function index(): void
    {
        $this->_check_usertoken();
        //$isvalid = (new LoginService($this->get_domain()))->is_valid($this->request->get_post(self::KEY_APIFYUSERTOKEN));
        $rule = ServiceFactory::get("Apify/Encrypts")->get_random_rule();
        $this->_get_json()->set_payload($rule)->show();
    }

}//Encrypt
