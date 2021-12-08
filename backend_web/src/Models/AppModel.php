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

    public function get_label(string $field): string
    {
        return $this->fields[$field]["label"] ?? "";
    }

    public function get_requestkey(string $field): string
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
        $nullables = [
            ModelType::DATE,
            ModelType::DATETIME,
            ModelType::INT,
            ModelType::DECIMAL
        ];
        foreach ($reqkeys as $requestkey) {
            $dbfield = $this->get_field($requestkey);
            $dbtype = $this->get_type($dbfield);
            if($dbfield) {
                $mapped[$dbfield] = ($value = trim($request[$requestkey]));
            }
            
            if(in_array($dbtype, $nullables) && !$value)
                $mapped[$dbfield] = null;
        }
        return $mapped;
    }

    public function do_match_keys(array $pkvals): bool
    {
        if(!$pkvals) return false;
        $keys = array_keys($pkvals);
        foreach ($this->pks as $pkfield) {
            if (!in_array($pkfield, $keys))
                return false;
            $value = $pkvals[$pkfield];
            if (!$value)
                return false;
        }
        return true;
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

    public function add_sysdelete(array &$request, string $updatedate, string $user, string $platform=PlatformType::WEB): self
    {
        $request[ModelType::DELETE_DATE] = date("Y-m-d H:i:s");
        $request[ModelType::DELETE_USER] = $user;
        $request[ModelType::DELETE_PLATFORM] = $platform;
        $request[ModelType::UPDATE_DATE] = $updatedate;
        return $this;
    }
}//AppModel
