<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Entities
 * @file AppEntity.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */
namespace App\Shared\Domain\Entities;

use App\Shared\Domain\Enums\EntityType;
use App\Shared\Domain\Enums\PlatformType;

abstract class AppEntity
{
    protected array $fields;
    protected array $pks;

    protected array $sysfields = [
        EntityType::INSERT_DATE,
        EntityType::INSERT_USER,
        EntityType::INSERT_PLATFORM,
        EntityType::UPDATE_DATE,
        EntityType::UPDATE_USER,
        EntityType::UPDATE_PLATFORM,
        EntityType::DELETE_DATE,
        EntityType::DELETE_USER,
        EntityType::DELETE_PLATFORM,
        EntityType::PROCESS_FLAG,
        EntityType::CRU_CSVNOTE,
        EntityType::IS_ERP_SENT,
        EntityType::IS_ENABLED,
        EntityType::I,
    ];

    public function get_fields(): array {return $this->fields;}
    public function get_pks(): array {return $this->pks;}

    public function in_fields(string $fieldname): bool
    {
        if(in_array($fieldname, $this->sysfields)) return true;

        return (bool) ($this->fields[$fieldname] ?? false);
    }

    public function get_label(string $field): string
    {
        return $this->fields[$field]["label"] ?? "";
    }

    public function get_requestkey(string $field): string
    {
        return $this->fields[$field][EntityType::REQUEST_KEY] ?? $field;
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
            if(($array[EntityType::REQUEST_KEY] ?? "") === $requestkey)
                return $field;
        }
        return "";
    }

    public function map_request(array $request): array
    {
        $reqkeys = array_keys($request);
        $mapped = [];
        $nullables = [
            EntityType::DATE,
            EntityType::DATETIME,
            EntityType::INT,
            EntityType::DECIMAL
        ];
        foreach ($reqkeys as $requestkey) {
            $dbfield = $this->get_field($requestkey);
            $dbtype = $this->get_type($dbfield);
            if($dbfield) {
                $mapped[$dbfield] = ($value = trim($request[$requestkey]));
            }
            
            if(in_array($dbtype, $nullables) && $value==="")
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
        $request[EntityType::INSERT_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::INSERT_USER] = $user;
        $request[EntityType::INSERT_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysupdate(array &$request, string $user, string $platform=PlatformType::WEB): self
    {
        $request[EntityType::UPDATE_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::UPDATE_USER] = $user;
        $request[EntityType::UPDATE_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysdelete(array &$request, string $updatedate, string $user, string $platform=PlatformType::WEB): self
    {
        $request[EntityType::DELETE_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::DELETE_USER] = $user;
        $request[EntityType::DELETE_PLATFORM] = $platform;
        $request[EntityType::UPDATE_DATE] = $updatedate;
        return $this;
    }

}//AppEntity
