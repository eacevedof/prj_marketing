<?php
namespace App\Open\PrivacyPolicy\Infrastructure\Controllers;

use App\Shared\Domain\Enums\ResponseType;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Exceptions\ForbiddenException;
use App\Shared\Infrastructure\Exceptions\NotFoundException;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\PrivacyPolicy\Application\PrivacyPolicyInfoService;
use App\Shared\Domain\Enums\PageType;

final class PrivacyPolicyInfoController extends OpenController
{
    public function index(): void
    {
        try {
            $terms = SF::get_callable(PrivacyPolicyInfoService::class)();
            $this->set_layout("open/mypromos/info")
                ->add_var(PageType::TITLE, $title = __("Privacy Policy"))
                ->add_var(PageType::H1, $title)
                ->add_var("result", $terms)
                ->render_nv();
        }
        catch (NotFoundException $e) {
            $this->add_header(ResponseType::NOT_FOUND)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Privacy Policy error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (ForbiddenException $e) {
            $this->add_header(ResponseType::FORBIDDEN)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Privacy Policy error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
        catch (Exception $e) {
            $this->add_header(ResponseType::INTERNAL_SERVER_ERROR)
                ->set_layout("open/mypromos/error")
                ->add_var(PageType::TITLE, $title = __("Privacy Policy error!"))
                ->add_var(PageType::H1, $title)
                ->add_var("error", $e->getMessage())
                ->add_var("code", $e->getCode())
                ->render();
        }
    }
}



