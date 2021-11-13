<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Helpers\AppHelper 
 * @file AppHelper.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Helpers;

use \Exception;

abstract class AppHelper
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    public function __construct(){;}

}//AppHelper
