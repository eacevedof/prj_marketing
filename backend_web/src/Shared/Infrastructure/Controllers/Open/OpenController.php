<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Controllers\OpenController
 * @file OpenController.php v1.0.0
 * @date 30-10-2021 14:33 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Controllers\Open;

use App\Shared\Domain\Enums\SessionType;
use App\Shared\Infrastructure\Controllers\AppController;
use App\Shared\Infrastructure\Traits\{RequestTrait, ResponseTrait, SessionTrait, ViewTrait};

abstract class OpenController extends AppController
{
    use RequestTrait;
    use ResponseTrait;
    use SessionTrait;
    use ViewTrait;

    public function __construct()
    {
        $this->_loadRequestComponentInstance();
        $this->_loadViewInstance();
        $this->_loadResponseComponentInstance();
        $this->_loadSessionComponentInstance();
        $this->_loadHttpLanguage();
        $this->setLayoutBySubPath("open/open/mypromos")
            ->addGlobalVar("authUser", $this->sessionComponent->get(SessionType::AUTH_USER));
    }

}//OpenController
