<?php
namespace App\Restrict\Subscriptions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Subscriptions\Application\SubscriptionsUpdateService;
use App\Restrict\Users\Domain\Enums\UserPolicyType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use \Exception;

final class SubscriptionsValidateQRController extends RestrictController
{

    //@modal
    public function edit(): void
    {
        $this->_if_noauth_tologin();

        if (!$this->auth->is_user_allowed(UserPolicyType::SUBSCRIPTIONS_WRITE))
            $this->add_var(PageType::TITLE, __("Unauthorized"))
                ->add_var(PageType::H1, __("Unauthorized"))
                ->add_var("ismodal",1)
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("403")
                ->render_nl();

        $this->add_var("ismodal",1);
        try {
            $this->set_template("qr-update-status")
                ->add_var(PageType::TITLE, __("Validate QR subscription"))
                ->add_var(PageType::H1, __("Validate QR subscription"));
            $this->view->render_nl();
        }
        catch (Exception $e) {
            $this->logerr($e->getMessage(),"subscriptionupdate.controller");
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("500")
                ->render_nl();
        }
    }//edit

    //@put
    public function update_status(): void
    {
        if (!$this->request->is_accept_json())
            $this->_get_json()
                ->set_code(ResponseType::BAD_REQUEST)
                ->set_error([__("Only type json for accept header is allowed")])
                ->show();

        try {
            $request = [
                "uuid" => $uuid = $this->request->get_post("uuid", ""),
                "exec_code" => $this->request->get_post("exec_code", ""),
                "notes" => $this->request->get_post("notes", ""),
            ];
            $update = SF::get_callable(SubscriptionsUpdateService::class, $request);
            $result = $update();
            $this->_get_json()->set_payload([
                "message"=> __("{0} {1} successfully updated", __("Subscription"), $uuid),
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

}//SubscriptionsUpdateController
