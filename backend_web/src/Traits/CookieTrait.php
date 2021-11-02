<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\SessionTrait
 * @file SessionTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */
namespace App\Traits;

use TheFramework\Components\Session\ComponentCookie;

trait CookieTrait
{
    private ?ComponentCookie $cookie = null;

    protected function _cookieinit(): SessionComponent
    {
        if(!$this->cookie) $this->cookie = new ComponentCookie();
        return $this->cookie;
    }

}//SessionTrait
