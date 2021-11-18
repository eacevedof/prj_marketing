<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\JsonTrait
 * @file JsonTrait.php 1.0.0
 * @date 18-11-2021 21:51 SPAIN
 * @observations
 */
namespace App\Traits;

use TheFramework\Helpers\HelperJson;

trait JsonTrait
{
    private function _get_json(): HelperJson
    {
        return new HelperJson();
    }

}//JsonTrait
