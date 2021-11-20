<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\AppModel 
 * @file AppModel.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */
namespace App\Models;

use App\Enums\ModelType;
use App\Enums\PlatformType;
use App\Enums\RequestType;

abstract class AppModel
{
    protected array $fields;
    protected array $pks;

    public function get_fields(): array {return $this->fields;}
    public function get_pks(): array {return $this->pks;}

    public function get_fieldnames(): array {return array_keys($this->fields);}

    public function get_label(string $field): string
    {
        return $this->fields[$field]["label"] ?? "";
    }

    public function get_postkey(string $field): string
    {
        return $this->fields[$field][ModelType::REQUEST_KEY] ?? $field;
    }

    public function get_type(string $field): string
    {
        return $this->fields[$field]["config"]["type"] ?? "";
    }

    public function get_length(string $field): ?int
    {
        return $this->fields[$field]["config"]["length"] ?? null;
    }

    public function is_field(string $field): bool
    {
         if(in_array($field, $this->get_fieldnames()))
             return true;

         $fields = array_map(function ($array){
             return $array[ModelType::REQUEST_KEY] ?? "";
         }, $this->fields);

         return in_array($field, $fields);
    }

    public function get_field(string $requestkey): string
    {
        foreach ($this->fields as $field => $array) {
            if(($array[ModelType::REQUEST_KEY] ?? "") === $requestkey)
                return $field;
        }
        return "";
    }

    public function map_request(array $request): array
    {
        $reqkeys = array_keys($request);
        $mapped = [];
        foreach ($reqkeys as $requestkey) {
            $dbfield = $this->get_field($requestkey);
            $dbtype = $this->get_type($dbfield);
            if($dbfield) {
                $mapped[$dbfield] = ($value = trim($request[$requestkey]));
            }
            
            if(in_array($dbtype,[ModelType::DATE,ModelType::DATETIME]) && !$value) {
                $mapped[$dbfield] = null;
            }
        }
        return $mapped;
    }

    public function add_sysinsert(array &$request, string $user, string $platform=PlatformType::WEB): self
    {
        $request[ModelType::INSERT_DATE] = date("Y-m-d H:i:s");
        $request[ModelType::INSERT_USER] = $user;
        $request[ModelType::INSERT_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysupdate(array &$request, string $user, string $platform=PlatformType::WEB): self
    {
        $request[ModelType::UPDATE_DATE] = date("Y-m-d H:i:s");
        $request[ModelType::UPDATE_USER] = $user;
        $request[ModelType::UPDATE_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysdelete(array &$request, string $user, string $platform=PlatformType::WEB): self
    {
        $request[ModelType::DELETE_DATE] = date("Y-m-d H:i:s");
        $request[ModelType::DELETE_USER] = $user;
        $request[ModelType::DELETE_PLATFORM] = $platform;
        return $this;
    }
}//AppModel
