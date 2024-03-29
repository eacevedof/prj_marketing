<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\ApifyController
 * @file ApifyController.php v1.2.0
 * @date 01-07-2021 20:14 SPAIN
 * @observations
 */

namespace App\Apify\Infrastructure\Controllers;

use TheFramework\Helpers\HelperJson;
use App\Shared\Domain\Enums\ResponseType;
use App\Services\Apify\Security\{LoginService, SignatureService};
use App\Shared\Infrastructure\Traits\{RequestTrait, ResponseTrait};

abstract class ApifyController extends AppController
{
    use RequestTrait;
    use ResponseTrait;

    protected const KEY_APIFYUSERTOKEN = "apify-usertoken";
    protected const KEY_API_SIGNATURE = "API_SIGNATURE";
    //protected const KEY_APIFYDOMAIN= "apify-origindomain";

    /**
     * Builds: request, response
     */
    public function __construct()
    {
        $this->_request_log();
        $this->_loadRequestComponentInstance();
        $this->_loadResponseComponentInstance();
    }

    protected function _check_signature(): bool
    {
        try {
            $post = $this->requestComponent->getPost();
            $domain = $this->get_domain(); //trata excepcion
            $token = $post[self::KEY_API_SIGNATURE] ?? "";
            unset($post[self::KEY_API_SIGNATURE]);
            $oServ = new SignatureService($domain, $post);
            return $oServ->is_valid($token);
        } catch (\Exception $e) {
            $this->logerr($e->getMessage(), "ApifyController.check_signature");
            $this->_getJsonInstanceFromResponse()->setResponseCode(HelperJson::CODE_UNAUTHORIZED)
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

    protected function _check_usertoken(): bool
    {
        try {
            $domain = $this->_get_domain(); //excepcion
            $token = $this->requestComponent->getPost(self::KEY_APIFYUSERTOKEN);
            $this->logd("domain:$domain,token:$token", "check_usertoken");
            $oServ = new LoginService($domain);
            $oServ->is_valid($token);
            return true;
        } catch (\Exception $e) {
            $this->logerr($e->getMessage(), "ApifyController.check_usertoken");
            $this->_getJsonInstanceFromResponse()->setResponseCode(ResponseType::FORBIDDEN)
                ->setErrors([$e->getMessage()])
                ->show();
        }
    }

    protected function _get_domain(): string
    {
        //$this->get_header();
        $domain = $_SERVER["REMOTE_HOST"] ?? "";
        //host es a donde se hace la patición
        //if (!$domain) $domain = $this->get_header("host");
        if (!$domain) {
            $domain = $this->requestComponent->getHeaderValueByKey("origin");
        }
        //if (!$domain) $domain = $_POST[self::KEY_APIFYDOMAIN] ?? "";
        if (!$domain) {
            throw new \Exception("No domain supplied");
        }
        $domain = str_replace(["https://","http://"], "", $domain);
        return $domain;
    }

}//ApifyController
