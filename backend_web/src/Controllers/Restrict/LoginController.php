<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict;
use App\Services\Restrict\LoginService;
use App\Factories\ServiceFactory as SF;
use TheFramework\Helpers\HelperJson;

final class LoginController extends RestrictController
{
    private LoginService $login;

    public function index(): void
    {
        $this->add_var("pagetitle", "LOGIN");
        $this->render();
    }

    public function access(): void
    {
        //sleep(15);
        //$this->sess_add("user", $this->get_post("email"))->add("pass",$this->get_post("password"));
        //$this->sess_destroy();

        $this->login = SF::get("Restrict\LoginService", $this->get_post());
        $this->logd("middle start");
        $oJson = new HelperJson();
        try{
            //$oServ = new LoginMiddleService($this->get_post());
            //$token = $oServ->get_token();
            $this->login->access();
            $oJson->set_payload(["token"=>$this->sess_get()])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"LoginController.middle");
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show(1);
        }
        $this->logd("middle end");
    }

}//LoginController
