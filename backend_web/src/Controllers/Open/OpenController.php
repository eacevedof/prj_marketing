<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\OpenController
 * @file OpenController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */
namespace App\Controllers\Open;

use App\Controllers\AppController;
use App\Enums\PolicyType;
use App\Enums\KeyType;
use App\Traits\ViewTrait;

final class OpenController extends AppController
{
    use ViewTrait;

    public function index(): void
    {
        $this->set_layout("open/open");
        $this->render([
            "a" => "hooolllaaa",
            "b" => "bbbbbbbbbb",
            "c" => "ccccccc"
        ], "open/xxx");
    }

    public function forbidden(): void
    {
        $this->set_layout("error/error")
            ->add_var(KeyType::PAGE_TITLE, __("Forbidden - 403"))
            ->add_var("h1",__("Unauthorized"))
        ;
        $this->render([],"error/403");
    }

}//OpenController
