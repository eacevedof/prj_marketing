<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInsertController
 * @file PromotionsInsertController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Promotions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Promotions\Application\PromotionsInsertService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use \Exception;

final class PromotionsInsertController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get(PicklistService::class);
    }

    //@modal (creation form)
    public function create(): void
    {
        if (!$this->auth->is_user_allowed(UserPolicyType::PROMOTIONS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();
        }

        //to-do business owners que tengan businessdata
        $businessowners =  ($this->auth->is_root() || $this->auth->is_sysadmin())
            ? $this->picklist->get_users_by_profile(UserProfileType::BUSINESS_OWNER)
            : [];

        $this->set_template("insert")
            ->add_var(PageType::CSRF, $this->csrf->get_token())
            ->add_var(PageType::H1, __("New promotion"))
            ->add_var("timezones", $this->picklist->get_timezones())
            ->add_var("businessowners", $businessowners)
            ->add_var("notoryes", $this->picklist->get_not_or_yes())
            ->render_nl();
    }

    //@post
    public function insert(): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        if (!$this->csrf->is_valid($this->_get_csrf()))
            $this->_get_json()
                ->set_code(ResponseType::FORBIDDEN)
                ->set_error([__("Invalid CSRF token")])
                ->show();

        try {
            $insert = SF::get_callable(PromotionsInsertService::class, $this->request->get_post());
            $result = $insert();
            $this->_get_json()->set_payload([
                "message" => __("{0} successfully created","Promotion"),
                "result" => $result,
            ])->show();
        }
        catch (FieldsException $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([
                    ["fields_validation" => $insert->get_errors()]
                ])
                ->show();
        }
        catch (Exception $e) {
            $this->_get_json()->set_code($e->getCode())
                ->set_error([$e->getMessage()])
                ->show();
        }

    }//insert

}//PromotionsInsertController
