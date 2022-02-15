<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Users\Infrastructure\Controllers\UsersUpdateController
 * @file UsersUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Users\Application\UsersInfoService;
use App\Restrict\Users\Application\UsersUpdateService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Domain\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use \Exception;

final class UsersUpdateController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get(PicklistService::class);
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->auth->is_user_allowed(UserPolicyType::USERS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();
        }

        $this->add_var("ismodal",1);
        try {
            $edit = SF::get(UsersInfoService::class, [$uuid]);
            $result = $edit->get_for_edit();
            $this->set_template("update")
                ->add_var(PageType::TITLE, __("Edit user {0}", $uuid))
                ->add_var(PageType::H1, __("Edit user {0}", $uuid))
                ->add_var(PageType::CSRF, $this->csrf->get_token())
                ->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->add_var("permissions", [])
                ->add_var("profiles", $this->picklist->get_profiles())
                ->add_var("parents", $this->picklist->get_users_by_profile(UserProfileType::BUSINESS_OWNER))
                ->add_var("countries", $this->picklist->get_countries())
                ->add_var("languages", $this->picklist->get_languages())
                ->render_nl();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("404")
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("505")
                ->render_nl();
        }
    }//modal

    //@patch
    public function update(string $uuid): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ExceptionType::CODE_UNAUTHORIZED)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        try {
            $request = ["uuid"=>$uuid] + $this->request->get_post();
            $update = SF::get_callable(UsersUpdateService::class, $request);
            $result = $update();
            $this->_get_json()->set_payload([
                "message"=> __("{0} successfully created", __("User")),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([["fields_validation" => $update->get_errors()]])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }
    }//patch

}//UsersUpdateController
