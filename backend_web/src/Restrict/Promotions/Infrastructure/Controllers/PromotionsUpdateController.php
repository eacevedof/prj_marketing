<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsUpdateController
 * @file PromotionsUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Promotions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Promotions\Application\PromotionsUpdateService;
use App\Restrict\Promotions\Application\PromotionsInfoService;
use App\Shared\Infrastructure\Enums\PolicyType;
use App\Shared\Infrastructure\Enums\PageType;
use App\Shared\Infrastructure\Enums\ResponseType;
use App\Shared\Infrastructure\Enums\ExceptionType;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use \Exception;

final class PromotionsUpdateController extends RestrictController
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
        if (!$this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE)) {
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();
        }

        $this->add_var("ismodal",1);
        try {
            $edit = SF::get(PromotionsInfoService::class, [$uuid]);
            $result = $edit->get_for_edit();
            $this->set_template("update")
                ->add_var(PageType::TITLE, __("Edit promotion {0}", $uuid))
                ->add_var(PageType::H1, __("Edit promotion {0}", $uuid))
                ->add_var(PageType::CSRF, $this->csrf->get_token())
                ->add_var("uuid", $uuid)
                ->add_var("result", $result)
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
    }//edit

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
            $update = SF::get_callable(PromotionsUpdateService::class, $request);
            $result = $update();
            $this->_get_json()->set_payload([
                "message"=> __("{0} {1} successfully updated", __("Promotion"), $uuid),
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
    }//update

}//PromotionsUpdateController
