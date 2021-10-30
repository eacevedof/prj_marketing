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
use App\Traits\ViewTrait;

final class OpenController extends AppController
{
    use ViewTrait;

    public function index(): void
    {
        $this->add_var("a","prrito");
        $this->add_var("b", "dddd");
        $this->add_var("c", "rrr");
        $this->render();
    }

}//OpenController
