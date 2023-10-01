<?php
/**
 * @link eduardoaf.com
 */

namespace App\Open\Home\Infrastructure\Controllers;

use App\Shared\Domain\Enums\PageType;
use App\Restrict\Auth\Application\CsrfService;
use App\Shared\Infrastructure\Factories\ServiceFactory as SF;
use App\Shared\Infrastructure\Controllers\Open\OpenController;
use App\Shared\Infrastructure\Components\Hierarchy\HierarchyComponent;

final class HomeController extends OpenController
{
    public function index(): void
    {
        $this->_testBug();
        $this->setLayoutBySubPath("open/mypromos/home")
            ->addGlobalVar(PageType::TITLE, $title = __("My Promotions"))
            ->addGlobalVar(PageType::H1, $title)
            ->addGlobalVar(PageType::CSRF, SF::getInstanceOf(CsrfService::class)->getCsrfToken())
            ->addGlobalVar("space", [])
            //->cache()
            ->render();
    }

    private function _testBug(): void
    {
        $ar = [
            0 =>
                [
                    'id' => 1,
                    'id_parent' => null,
                ],
            1 =>
                [
                    'id' => 2,
                    'id_parent' => null,
                ],
            2 =>
                [
                    'id' => 3,
                    'id_parent' => null,
                ],
            3 =>
                [
                    'id' => 4,
                    'id_parent' => 3,
                ],
            4 =>
                [
                    'id' => 44,
                    'id_parent' => null,
                ],
            5 =>
                [
                    'id' => 45,
                    'id_parent' => 44,
                ],
            6 =>
                [
                    'id' => 46,
                    'id_parent' => null,
                ],
            7 =>
                [
                    'id' => 47,
                    'id_parent' => null,
                ],
            8 =>
                [
                    'id' => 48,
                    'id_parent' => 47,
                ],
        ];
        $id = 44;

        $r = (new HierarchyComponent)->getTopParent($id, $ar);
    }
}
