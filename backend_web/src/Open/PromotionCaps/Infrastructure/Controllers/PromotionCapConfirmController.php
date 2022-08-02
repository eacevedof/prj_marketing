<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapsConfirmService;
use App\Open\Business\Application\BusinessSpaceService;
use App\Shared\Domain\Enums\PageType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;
use \Exception;

final class PromotionCapConfirmController extends OpenController
{
    public function confirm(string $businessslug, string $subscriptionuuid): void
    {
        if (!($businessslug && $subscriptionuuid))
            $this->set_layout("open/mypromos/error")
                ->add_header($code = ResponseType::BAD_REQUEST)
                ->add_var(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", __("Missing partner and/or subscription code"))
                ->add_var("code", $code)
                ->render_nv();

        $istest = ($this->request->get_get("mode", "")==="test");
        $space = SF::get(BusinessSpaceService::class, ["_test_mode" => $istest])->get_data_by_promocap($subscriptionuuid);
        if (!$space)
            $this->add_header($code = ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", __("{0} not found!", __("Subscription")))
                ->add_var("code", $code)
                ->render_nv();

        try {
            $insert = SF::get_callable(PromotionCapsConfirmService::class, [
                "promotionuuid" => $space["promocode"] ?? "",
                "subscriptionuuid" => $subscriptionuuid,
                "_test_mode" => $istest,
            ]);
            $result = $insert();
            $this->set_layout("open/mypromos/success")
                ->add_var(PageType::TITLE, $title = __("Subscription confirmation success!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("success",  [
                    ["p" => __("<b>{0}</b>. You have successfully confirmed your subscription to <b>“{1}“</b>", $result["username"], $result["promotion"])],
                    ["p" => __("Please check your email inbox. You will receive a voucher code which you should show it at <b>{0}</b>", $result["business"])],
                ]);

            unset($insert, $result, $promotionuuid, $subscriptionuuid);
            $this->view->render_nv();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription confirmation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }
}



