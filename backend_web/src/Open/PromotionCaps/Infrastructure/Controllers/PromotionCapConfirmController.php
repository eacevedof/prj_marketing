<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapsConfirmService;
use App\Shared\Domain\Enums\PageType;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapConfirmController extends OpenController
{
    public function confirm(string $promotionuuid, string $subscriptionuuid): void
    {
        if (!($promotionuuid && $subscriptionuuid))
            $this->set_layout("open/error")
                ->add_header($code = ResponseType::BAD_REQUEST)
                ->add_var(PageType::TITLE, __("Subscription error!"))
                ->add_var(PageType::H1, __("Bad Request"))
                ->add_var("error", __("Missing promotion and/or subscription code"))
                ->add_var("code", $code)
                ->render();
        try {
            $insert = SF::get_callable(PromotionCapsConfirmService::class, [
                "promotionuuid" => $promotionuuid,
                "subscriptionuuid" => $subscriptionuuid,
                "_test_mode" => $this->request->get_get("mode", "")==="test",
            ]);
            $result = $insert();
            $this->set_layout("open/success")
                ->add_var(PageType::TITLE, __("Subscription success!"))
                ->add_var(PageType::H1, htmlentities($result["promotion"]))
                ->add_var("result", $result);

            unset($insert, $result, $promotionuuid, $subscriptionuuid);
            $this->render();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->set_layout("open/error")
                ->add_var(PageType::TITLE, $title = __("Subscription error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/error")
                ->add_var(PageType::TITLE, $title = __("Unexpected error"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



