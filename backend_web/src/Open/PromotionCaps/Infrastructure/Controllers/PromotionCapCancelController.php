<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapsCancelService;
use App\Shared\Domain\Enums\PageType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapCancelController extends OpenController
{
    public function cancel(string $promotionuuid, string $subscriptionuuid): void
    {
        if (!($promotionuuid && $subscriptionuuid))
            $this->add_header(ResponseType::BAD_REQUEST)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", __("Missing promotion and/or subscription code"))
                ->add_var("code", ResponseType::BAD_REQUEST)
                ->render_nv();
        try {
            $insert = SF::get_callable(PromotionCapsCancelService::class, [
                "promotionuuid" => $promotionuuid,
                "subscriptionuuid" => $subscriptionuuid,
                "_test_mode" => $this->request->get_get("mode", "")==="test",
            ]);
            $result = $insert();
            $this->set_layout("open/mypromos/success")
                ->add_var(PageType::TITLE, $title = __("Subscription cancellation successfully done!"))
                ->add_var(PageType::H1, $title)
                ->add_var("success",  [
                    ["p" => __("<b>{0}</b>. You have successfully cancelled your subscription to <b>“{1}&rdquo;</b>", $result["username"], $result["promotion"])],
                ]);
            unset($insert, $result, $promotionuuid, $subscriptionuuid);
            $this->view->render_nv();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Subscription cancellation error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }
}



