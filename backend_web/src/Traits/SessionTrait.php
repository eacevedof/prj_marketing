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
use App\Factories\SessionFactory as SsF;

trait SessionTrait
{
    /**
     * @var SessionComponent
     */
    private $session = null;

    protected function sess_load(): SessionComponent
    {
        if(!$this->session) $this->session = SsF::get();
        return $this->session;
    }

    protected function sess_get(string $key=""){return $this->session->get($key);}

    protected function sess_getonce(string $key){return $this->session->get_once($key);}

    protected function sess_add(string $key, $mxvalue): SessionComponent
    {
        $this->session->add($key, $mxvalue);
        return $this->session;
    }

    protected function sess_remove(string $key): SessionComponent
    {
        $this->session->remove($key);
        return $this->session;
    }

}//SessionTrait
