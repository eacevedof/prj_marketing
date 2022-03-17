<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\LoginController
 * @file LoginController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Restrict\Login\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Login\Application\LoginService;
use App\Restrict\Users\Domain\Enums\UserPreferenceType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\UrlType;
use App\Shared\Domain\Enums\ResponseType;
use \Exception;

final class LoginController extends RestrictController
{
    public function index(): void
    {
        $this->add_var(PageType::TITLE, __("Login"))
            ->add_var(PageType::H1, __("Login"))
            ->add_var(PageType::CSRF, $this->csrf->get_token())
            ->render();
    }

    //@post
    public function access(): void
    {
        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        try {
            $post = $this->request->get_post();
            $result = SF::get(LoginService::class, $post)->get_access();
            $this->_get_json()
                ->set_payload([
                    "message"=>__("auth ok"),
                    "lang" => $result["lang"],
                    UserPreferenceType::URL_DEFAULT_MODULE => $result[UserPreferenceType::URL_DEFAULT_MODULE]
                ])->show();
        }
        catch (Exception $e)
        {
            $this->_get_json()
                ->set_code(ResponseType::UNAUTHORIZED)
                ->set_error([$e->getMessage()])
                ->show();
        }
    }

    public function logout(): void
    {
        $this->_load_session()->destroy();
        $this->response->location(UrlType::LOGIN_FORM);
    }
}//LoginController
