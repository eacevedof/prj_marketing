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
use App\Traits\RequestTrait;
use App\Traits\ViewTrait;
use App\Traits\ResponseTrait;

abstract class OpenController extends AppController
{
    use RequestTrait;
    use ViewTrait;
    use ResponseTrait;

    public function __construct()
    {
        $this->_load_request();
        $this->_load_view();
        $this->_load_response();
        $this->set_layout("open/open");
    }

}//OpenController

