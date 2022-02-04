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
use App\Shared\Infrastructure\Components\Response\ResponseComponent;
use App\Shared\Infrastructure\Enums\ResponseType;
use App\Shared\Infrastructure\Factories\ComponentFactory;
use TheFramework\Helpers\HelperJson;

trait ResponseTrait
{
    protected ?ResponseComponent $response = null;

    protected function _load_response(): ResponseComponent
    {
        if (!$this->response)
            $this->response = ComponentFactory::get(ResponseComponent::class);
        return $this->response;
    }

    protected function _get_json(): HelperJson
    {
        return $this->response->json();
    }
}//ResponseTrait
