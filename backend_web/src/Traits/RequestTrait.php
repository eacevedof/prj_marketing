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
use App\Enums\RequestType;

trait RequestTrait
{
    private array $request;
    
    private function _get_without_operations(array $request=[]): array
    {
        if(!$request) $request = $this->request;
        if(!$request) return [];
        
        $without = [];
        foreach ($request as $key=>$value)
            if(!substr($key,0,1)=="_")
                $without[$key] = $value;
        return $without;
    }

    private function _get_csrf(array $request=[]): string
    {
        if(!$request) $request = $this->request;
        return $request[RequestType::CSRF] ?? "";
    }

    private function _get_action(array $request=[]): string
    {
        if(!$request) $request = $this->request;
        return $request[RequestType::ACTION] ?? "";
    }

}//RequestTrait
