<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Infrastructure\Helpers\AppHelper
 * @file AppHelper.php 1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 * @tags: #apify
 */

namespace App\Shared\Infrastructure\Helpers;

use App\Shared\Infrastructure\Traits\{EnvTrait, ErrorTrait, LogTrait};

abstract class AppHelper
{
    use EnvTrait;
    use ErrorTrait;
    use LogTrait;

}//AppHelper
