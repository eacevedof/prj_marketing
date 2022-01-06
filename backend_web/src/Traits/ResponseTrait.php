<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ResponseTrait
 * @file ResponseTrait.php 1.0.0
 * @date 18-11-2021 21:51 SPAIN
 * @observations
 */
namespace App\Traits;
use App\Components\Response\ResponseComponent;
use App\Enums\ResponseType;
use App\Factories\ComponentFactory;
use TheFramework\Helpers\HelperJson;

trait ResponseTrait
{
    protected ?ResponseComponent $response = null;

    protected function _load_response(): ResponseComponent
    {
        if (!$this->response)
            $this->response = ComponentFactory::get("Response/Response");
        return $this->response;
    }

    protected function _get_json(): HelperJson
    {
        return $this->response->json();
    }
}//ResponseTrait
