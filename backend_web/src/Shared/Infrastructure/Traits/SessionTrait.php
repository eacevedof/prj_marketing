<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\SessionTrait
 * @file SessionTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

use App\Shared\Infrastructure\Components\Session\SessionComponent;
use App\Shared\Infrastructure\Factories\Specific\SessionFactory as SsF;

trait SessionTrait
{
    protected ?SessionComponent $sessionComponent = null;

    protected function _loadSessionComponentInstance(): void
    {
        if (!$this->sessionComponent) {
            $this->sessionComponent = SsF::get();
        }
    }

}//SessionTrait
