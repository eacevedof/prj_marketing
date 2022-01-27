<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\Xxxs\XxxsUpdateController
 * @file XxxsUpdateController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict\Xxxs;

use App\Controllers\Restrict\RestrictController;
use App\Factories\ServiceFactory as SF;
use App\Services\Common\PicklistService;
use App\Enums\PolicyType;
use App\Enums\PageType;
use App\Enums\ResponseType;
use App\Enums\ExceptionType;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\FieldsException;
use \Exception;

final class XxxsUpdateController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get("Common\Picklist");
    }

    //@modal
    public function edit(string $uuid): void
    {
        if (!$this->auth->is_user_allowed(PolicyType::PROMOTIONS_WRITE)) {
            $this->set_template("/error/403")
                ->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->render_nl();
        }

        $this->add_var("ismodal",1);
        try {
            $edit = SF::get("Restrict\Xxxs\XxxsInfo", [$uuid]);
            $result = $edit->get_for_edit();
            $this->set_template("xxxs/update")
                ->add_var(PageType::TITLE, __("Edit xxx {0}", $uuid))
                ->add_var(PageType::H1, __("Edit xxx {0}", $uuid))
                ->add_var(PageType::CSRF, $this->csrf->get_token())
                ->add_var("uuid", $uuid)
                ->add_var("result", $result)
                ->render_nl();
        }
        catch (NotFoundException $e) {
            $this->set_template("/error/404")
                ->add_header(ResponseType::NOT_FOUND)
                ->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->set_template("/error/403")
                ->add_header(ResponseType::FORBIDDEN)
                ->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (Exception $e) {
            $this->set_template("/error/500")
                ->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::TITLE, $e->getMessage())
                ->add_var(PageType::H1, $e->getMessage())
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
            $update = SF::get_callable("Restrict\Xxxs\XxxsUpdate", $request);
            $result = $update();
            $this->_get_json()->set_payload([
                "message"=> __("{0} {1} successfully updated", __("Xxx"), $uuid),
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

}//XxxsUpdateController
