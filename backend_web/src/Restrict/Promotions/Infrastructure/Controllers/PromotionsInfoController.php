<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Infrastructure\Controllers\PromotionsInfoController
 * @file PromotionsInfoController.php v1.0.0
 * @date 23-01-2022 10:22 SPAIN
 * @observations
 */
namespace App\Restrict\Promotions\Infrastructure\Controllers;

use App\Shared\Infrastructure\Controllers\Restrict\RestrictController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Picklist\Application\PicklistService;
use App\Restrict\Promotions\Application\PromotionsInfoService;
use App\Shared\Domain\Enums\PageType;
use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use \Exception;

final class PromotionsInfoController extends RestrictController
{
    private PicklistService $picklist;
    
    public function __construct()
    {
        parent::__construct();
        $this->picklist = SF::get(PicklistService::class);
    }

    //@modal
    public function info(string $uuid): void
    {
        $this->add_var(PageType::TITLE, __("Promotion info"))
            ->add_var(PageType::H1, __("Promotion info"))
            ->add_var("ismodal",1);

        try {
            $info = SF::get_callable(PromotionsInfoService::class, [$uuid]);
            $result = $info();
            $this->add_var("uuid", $uuid)
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
    }

}//PromotionsInfoController
