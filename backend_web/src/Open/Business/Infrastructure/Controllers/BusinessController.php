<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Open\Business\Infrastructure\Controllers\BusinessController
 * @file BusinessController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Open\Business\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\Business\Application\BusinessSpaceService;
use App\Open\Business\Application\BusinessSpacePageService;
use App\Shared\Domain\Enums\PageType;
use Exception;

final class BusinessController extends OpenController
{
    public function index(string $businessslug): void
    {
        if (!$businessslug)
            $this->set_layout("open/mypromos/error")
                ->add_header($code = ResponseType::BAD_REQUEST)
                ->add_var(PageType::TITLE, $title = __("Partner space error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", __("Missing partner"))
                ->add_var("code", $code)
                ->render_nv();

        try {
            $space = SF::get(BusinessSpaceService::class)->get_data_by_slug($businessslug);
            $this->set_layout("open/mypromos/business")
                ->add_var(PageType::TITLE, $title = $space["business"])
                ->add_var(PageType::H1, $title)
                ->add_var("space", $space)
                ->add_var(
                    "result",
                    SF::get(BusinessSpacePageService::class)->get_page_by_businessslug($businessslug)
                )
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Partner space error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Partner space error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("space", [])
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render_nv();
        }
    }


}//BusinessController



