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

final class PromotionCapsConfirmController extends OpenController
{
    public function confirm(string $promouuid, string $subsuuid): void
    {
        if (!($subsuuid && $promouuid))
            $this->set_layout("open/empty")
                ->add_header(ResponseType::BAD_REQUEST)
                ->add_var(PageType::H1, $e->getMessage())
                ->set_foldertpl("Open/Errors/Infrastructure/Views")
                ->set_template("500");

        try {
            $insert = SF::get_callable(PromotionCapsConfirmService::class, [
                "promouuid"=>$promouuid,
                "subsuuid"=>$subsuuid
            ]);
            $result = $insert();
            $this->set_layout("open/empty")
                ->add_var(PageType::H1, htmlentities($result["promotion"]))
                ->add_var("result", $result);

            unset($insert, $result, $promouuid, $subsuuid);
            $this->view->render();
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



