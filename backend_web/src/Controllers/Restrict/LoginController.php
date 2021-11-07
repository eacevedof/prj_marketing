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
use App\Enums\Key;
use App\Services\Restrict\LoginService;
use App\Factories\ServiceFactory as SF;
use TheFramework\Helpers\HelperJson;

final class LoginController extends RestrictController
{
    private LoginService $login;

    public function index(): void
    {
        $this
            ->add_var(Key::PAGE_TITLE, __("LOGIN"))
            ->add_var(Key::KEY_CSRF, $this->csrf->get_token());

        $this->render();
    }

    //@post
    public function access(): void
    {
        $oJson = new HelperJson();
        try {
            $post = $this->get_post();
            $this->csrf->is_valid($post["csrf"]);
            $this->login = SF::get("Restrict\Login", $post);

            $result = $this->login->in();
            $oJson->set_payload([
                        "message"=>__("auth ok"),
                        "lang" => $result["lang"]
                    ])->show();
        }
        catch (\Exception $e)
        {
            $this->logerr($e->getMessage(),"LoginController.access");
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show(1);
        }
    }
}//LoginController
