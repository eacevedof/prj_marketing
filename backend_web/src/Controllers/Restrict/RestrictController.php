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

use App\Controllers\AppController;
use App\Services\Restrict\LoginService;
use App\Traits\ViewTrait;
use App\Factories\ServiceFactory as SF;

abstract class RestrictController extends AppController
{
    use ViewTrait;

    public function __construct()
    {

        $this->set_layout("restrict/restrict");
    }


}//RestrictController
