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

use TheFramework\Components\Session\ComponentCookie;

trait CookieTrait
{
    private ?ComponentCookie $componentCookie = null;

    protected function _loadComponentCookieInstance(): void
    {
        if (!$this->componentCookie) {
            $this->componentCookie = new ComponentCookie;
        }
    }

}//CookieTrait
