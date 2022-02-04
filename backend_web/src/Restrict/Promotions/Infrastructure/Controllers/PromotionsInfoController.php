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
use App\Shared\Infrastructure\Enums\PageType;
use App\Shared\Infrastructure\Enums\ResponseType;
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
            $this->set_template("/error/404")
                ->add_header(ResponseType::NOT_FOUND)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (ForbiddenException $e) {
            $this->set_template("/error/403")
                ->add_header(ResponseType::FORBIDDEN)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
        catch (Exception $e) {
            $this->set_template("/error/500")
                ->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->add_var(PageType::H1, $e->getMessage())
                ->render_nl();
        }
    }

}//PromotionsInfoController
