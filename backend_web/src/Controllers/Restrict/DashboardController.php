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

use App\Enums\KeyType;
use App\Enums\UrlType;
use App\Factories\ServiceFactory as SF;

final class DashboardController extends RestrictController
{
    public function index(): void
    {
        if(!$this->authuser)
            $this->location(UrlType::FORBIDDEN);

        $service = SF::get_callable("Restrict\\Dashboard");
        $this
            ->add_var(KeyType::PAGE_TITLE, __("Dashboard"))
            ->add_var("modules", $service())
        ;

        $this->render([
            "h1" => $this->authuser["description"]
        ]);
    }

}//DashboardController
