<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\RequestTrait
 * @file RequestTrait.php 1.0.0
 * @date 18-11-2021 21:51 SPAIN
 * @observations
 */
namespace App\Traits;
use App\Components\Request\RequestComponent;
use App\Enums\RequestType;
use App\Factories\ComponentFactory;

trait RequestTrait
{
    protected RequestComponent $request;

    protected function _load_request(): void
    {
        $this->request = ComponentFactory::get("Request/Request");
    }

    private function _get_without_operations(array $request=[]): array
    {
        if (!$request) $request = $this->request->get_post();
        
        $without = [];
        foreach ($request as $key=>$value)
            if(substr($key,0,1)!="_")
                $without[$key] = is_string($value) && trim($value)==="" ? null : trim($value);

        return $without;
    }

    protected function _get_csrf(array $request=[]): string
    {
        if(!$request) $request = $this->request->get_post();
        return $request[RequestType::CSRF] ?? "";
    }

}//RequestTrait
