<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\Restrict\RestrictController
 * @file RestrictController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Restrict;
use App\Enums\PolicyType;
use App\Enums\SessionType;
use App\Enums\UrlType;

final class PromotionsController extends RestrictController
{
    public function index(): void
    {
        if (!$this->auth->is_user_allowed(PolicyType::DASHBOARD_READ))
            $this->response->location(UrlType::FORBIDDEN);

        $this->add_var(PageType::TITLE, __("Promotions"));

        $this->render([
            "h1" => __("Promotions")
        ]);
    }

    public function detail(string $id)
    {
        if (!$this->auth->is_user_allowed(PolicyType::DASHBOARD_READ))
            $this->response->location(UrlType::FORBIDDEN);

        $this->add_var(PageType::TITLE, __("Promotions detail"));
        $this->render([
            "h1" => __("Promotion detail {0}", $id)
        ]);
    }

}//PromotionController
