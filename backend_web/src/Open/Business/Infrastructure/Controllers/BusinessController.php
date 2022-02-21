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

use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Open\Business\Application\BusinessInfoService;
use App\Shared\Domain\Enums\PageType;


final class BusinessController extends OpenController
{
    public function index(string $slug): void
    {
        //dd($this->request->get_get());
        $business = SF::get_callable(BusinessInfoService::class, [$slug]);
        dd($business);
        $this->add_var(PageType::TITLE, __("Home"))
            ->add_var(PageType::H1, __("Home"))
            ->cache()
            ->render();
    }


}//BusinessController



