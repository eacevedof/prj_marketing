<?php
/**
 * @link eduardoaf.com
 */
namespace App\Open\Home\Infrastructure\Controllers;

use App\Shared\Infrastructure\Components\Hierarchy\HierarchyComponent;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Restrict\Auth\Application\CsrfService;
use App\Shared\Domain\Enums\PageType;

final class HomeController extends OpenController
{
    public function index(): void
    {
        $this->_test_bug();
        $this->set_layout("open/mypromos/home")
            ->add_var(PageType::TITLE, $title = __("My Promotions"))
            ->add_var(PageType::H1, $title)
            ->add_var(PageType::CSRF, SF::get(CsrfService::class)->get_token())
            ->add_var("space", [])
            //->cache()
            ->render();
    }

    private function _test_bug(): void
    {
        $ar = array (
            0 =>
                array (
                    'id' => 1,
                    'id_parent' => NULL,
                ),
            1 =>
                array (
                    'id' => 2,
                    'id_parent' => NULL,
                ),
            2 =>
                array (
                    'id' => 3,
                    'id_parent' => NULL,
                ),
            3 =>
                array (
                    'id' => 4,
                    'id_parent' => 3,
                ),
            4 =>
                array (
                    'id' => 44,
                    'id_parent' => NULL,
                ),
            5 =>
                array (
                    'id' => 45,
                    'id_parent' => 44,
                ),
            6 =>
                array (
                    'id' => 46,
                    'id_parent' => NULL,
                ),
            7 =>
                array (
                    'id' => 47,
                    'id_parent' => NULL,
                ),
            8 =>
                array (
                    'id' => 48,
                    'id_parent' => 47,
                ),
        );
        $id = 44;

        $r = (new HierarchyComponent())->get_topparent($id, $ar);
    }
}



