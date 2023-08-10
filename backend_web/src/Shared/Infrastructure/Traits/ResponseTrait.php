<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ResponseTrait
 * @file ResponseTrait.php 1.0.0
 * @date 18-11-2021 21:51 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

use TheFramework\Helpers\HelperJson;
use App\Shared\Infrastructure\Factories\ComponentFactory;
use App\Shared\Infrastructure\Components\Response\ResponseComponent;

trait ResponseTrait
{
    protected ?ResponseComponent $responseComponent = null;

    protected function _loadResponseComponentInstance(): ResponseComponent
    {
        if (!$this->responseComponent) {
            $this->responseComponent = ComponentFactory::getInstanceOf(ResponseComponent::class);
        }
        return $this->responseComponent;
    }

    protected function _getJsonInstanceFromResponse(): HelperJson
    {
        return $this->responseComponent->json();
    }
}//ResponseTrait
