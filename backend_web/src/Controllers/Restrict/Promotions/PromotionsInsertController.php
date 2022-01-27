<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\Promotions\PromotionsInsertController
 * @file PromotionsInsertController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict\Promotions;

use App\Controllers\Restrict\RestrictController;
use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Enums\PolicyType;
use App\Enums\PageType;
use App\Enums\ProfileType;
use App\Enums\ResponseType;
use App\Exceptions\FieldsException;
use \Exception;

final class PromotionsInsertController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    //@modal
    public function create(): void
    {
        if (!$this->auth->is_promotion_allowed(PolicyType::USERS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_template("/error/403")
                ->render_nl();
        }

        $this->add_var(PageType::CSRF, $this->csrf->get_token())
            ->set_template("promotions/insert")
            ->add_var(PageType::H1,__("New promotion"))
            ->add_var("profiles", $this->picklist->get_profiles())
            ->add_var("parents", $this->picklist->get_promotions_by_profile(ProfileType::BUSINESS_OWNER))
            ->add_var("countries", $this->picklist->get_countries())
            ->add_var("languages", $this->picklist->get_languages())
            ->render_nl();
    }//create

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
            $insert = SF::get_callable("Restrict\Promotions\PromotionsInsert", $this->request->get_post());
            $result = $insert();
            $this->_get_json()->set_payload([
                "message" => __("Promotion successfully created"),
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
