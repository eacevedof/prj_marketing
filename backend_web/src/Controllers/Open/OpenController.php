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
use App\Enums\KeyType;
use App\Traits\RequestTrait;
use App\Traits\ResponseTrait;
use App\Traits\ViewTrait;


abstract class OpenController extends AppController
{
    use ViewTrait;
    use RequestTrait;
    use ResponseTrait;

    public function __construct()
    {
        $this->_load_request();
        $this->_load_response();
        $this->set_layout("open/open");
    }

    public function forbidden(): void
    {
        $this->set_layout("error/error")
            ->add_var(KeyType::PAGE_TITLE, __("Forbidden - 403"))
            ->add_var("h1", __("Unauthorized"))
        ;

        $this->render([],"error/403");
    }

}//OpenController

