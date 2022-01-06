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
use App\Factories\ServiceFactory as SF;
use App\Enums\PreferenceType;
use App\Enums\SessionType;
use App\Enums\ExceptionType;
use App\Enums\UrlType;
use \Exception;

final class LoginController extends RestrictController
{
    public function index(): void
    {
        $this
            ->add_var(SessionType::PAGE_TITLE, __("Login"))
            ->add_var(SessionType::KEY_CSRF, $this->csrf->get_token());
        $this->render();
    }

    //@post
    public function access(): void
    {
        if (!$this->csrf->is_valid($this->_get_csrf())) {
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();
        }

        try {
            $post = $this->request->get_post();
            $result = SF::get("Restrict\Login", $post)->in();
            $this->_get_json()
                ->set_payload([
                    "message"=>__("auth ok"),
                    "lang" => $result["lang"],
                    PreferenceType::URL_DEFAULT_MODULE => $result[PreferenceType::URL_DEFAULT_MODULE]
                ])->show();
        }
        catch (Exception $e)
        {
            $this->logerr($e->getMessage(),"LoginController.access");
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    public function logout(): void
    {
        $this->_load_session()->destroy();
        $this->response->location(UrlType::ON_LOGOUT);
    }
}//LoginController
