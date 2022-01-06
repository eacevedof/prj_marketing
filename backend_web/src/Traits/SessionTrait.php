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
use App\Components\Session\SessionComponent;
use App\Factories\Specific\SessionFactory as SsF;

/**
 * Trait SessionTrait
 * @package App\Traits
 * this->session, _load_session()
 */
trait SessionTrait
{
    private ?SessionComponent $session = null;

    protected function _load_session(): SessionComponent
    {
        if(!$this->session) $this->session = SsF::get();
        return $this->session;
    }

}//SessionTrait
