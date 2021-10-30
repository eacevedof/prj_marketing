<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Views\AppView
 * @file AppView.php 1.0.0
 * @date 30-10-2021 15:00 SPAIN
 * @observations
 * @tags: #apify
 */
namespace App\Views;

use App\Traits\ErrorTrait;
use App\Traits\LogTrait;
use App\Traits\EnvTrait;
use \Exception;

final class AppView
{
    use ErrorTrait;
    use LogTrait;
    use EnvTrait;

    public function __construct(){;}

    public function render(): void
    {
        die("rendred");
    }

    protected function _exception(string $message, int $code=500): void
    {
        $this->logerr($message,"app-service.exception");
        throw new Exception($message, $code);
    }
}//AppView
