<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\PromotionCaps\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PromotionCaps\Application\PromotionCapsConfirmService;
use App\Open\PromotionCaps\Domain\Enums\RequestActionType;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Infrastructure\Exceptions\FieldsException;
use App\Open\PromotionCaps\Domain\Errors\PromotionCapException;

final class PromotionCapsConfirmController extends OpenController
{
    public function confirm(string $promotionuuid, string $subscriptionuuid): void
    {
        if (!($promotionuuid && $subscriptionuuid))
            $this->set_layout("open/empty")
                ->add_header(ResponseType::BAD_REQUEST)
                ->add_var(PageType::H1, __("Bad Request"))
                ->add_var("description", __("Missing promotion and/or subscription code"))
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("400")
                ->render();
        try {
            $insert = SF::get_callable(PromotionCapsConfirmService::class, [
                "promotionuuid" => $promotionuuid,
                "subscriptionuuid" => $subscriptionuuid
            ]);
            $result = $insert();
            $this->add_var(PageType::H1, htmlentities($result["promotion"]))
                ->add_var("result", $result);

            unset($insert, $result, $promotionuuid, $subscriptionuuid);
            $this->view->render_nl();
        }
        catch (PromotionCapException $e) {
            $this->add_header($e->getCode())
                ->add_var(PageType::H1, __("Warning!"))
                ->add_var("error", $e->getMessage())
                ->render_nl();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("500")
                ->render_nl();
        }
    }
}



