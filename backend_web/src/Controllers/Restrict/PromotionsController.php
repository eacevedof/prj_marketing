<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\PromotionsController
 * @file PromotionsController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict;
use App\Enums\PolicyType;
use App\Enums\PageType;
use App\Enums\UrlType;

final class PromotionsController extends RestrictController
{
    public function index(): void
    {
        if (!$this->auth->is_user_allowed(PolicyType::DASHBOARD_READ))
            $this->response->location(UrlType::FORBIDDEN);

        $this
            ->add_var(PageType::TITLE, __("Promotions"))
            ->add_var(PageType::H1, __("Promotions"))
            ->render();
    }

    public function detail(string $id)
    {
        if (!$this->auth->is_user_allowed(PolicyType::DASHBOARD_READ))
            $this->response->location(UrlType::FORBIDDEN);

        $this->add_var(PageType::TITLE, __("Promotions detail"))
            ->add_var(PageType::H1, __("Promotions detail"))
            ->render()
        ;
    }

}//PromotionController
