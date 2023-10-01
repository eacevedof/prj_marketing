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

use ReflectionObject;
use App\Shared\Domain\Enums\{EntityType, PlatformType};

abstract class AppEntity
{
    protected array $fields;
    protected array $pks;

    protected array $sysFields = [
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

    public function getFields(): array
    {
        return $this->fields;
    }
    public function getPks(): array
    {
        return $this->pks;
    }

    public function isInFields(string $fieldName): bool
    {
        if(in_array($fieldName, $this->sysFields)) {
            return true;
        }

        return (bool) ($this->fields[$fieldName] ?? false);
    }

    public function getLabel(string $fieldName): string
    {
        return $this->fields[$fieldName]["label"] ?? "";
    }

    public function getRequestKeyByFieldName(string $fieldName): string
    {
        return $this->fields[$fieldName][EntityType::REQUEST_KEY] ?? $fieldName;
    }

    public function getTypeByFieldName(string $fieldName): string
    {
        return $this->fields[$fieldName]["config"]["type"] ?? "";
    }

    public function getLength(string $fieldName): ?int
    {
        return $this->fields[$fieldName]["config"]["length"] ?? null;
    }

    public function getFieldNamedByRequestKey(string $requestKey): string
    {
        foreach ($this->fields as $fieldName => $array) {
            if(($array[EntityType::REQUEST_KEY] ?? "") === $requestKey) {
                return $fieldName;
            }
        }
        return "";
    }

    private function _getDtSanitized(string $dt): string
    {
        if (strlen($dt) == 16) {
            $dt = "$dt:00";
        }
        if (str_contains($dt, "T")) {
            $dt = str_replace("T", " ", $dt);
        }
        return $dt;
    }

    public function getAllKeyValueFromRequest(array|object $request): array
    {
        $nullables = [
            EntityType::DATE,
            EntityType::DATETIME,
            EntityType::INT,
            EntityType::DECIMAL
        ];

        if (is_object($request))
            $request = $this->getObjectAsArrayInSnakeCase($request);

        $requestKeys = array_keys($request);
        $mapped = [];
        foreach ($requestKeys as $requestKey) {
            $dbFieldName = $this->getFieldNamedByRequestKey($requestKey);
            $dbFieldType = $this->getTypeByFieldName($dbFieldName);
            $fieldValue = "";
            if ($dbFieldName) {
                $fieldValue = trim($request[$requestKey] ?? "");
                if ($dbFieldType === EntityType::DATETIME) {
                    $fieldValue = $this->_getDtSanitized($fieldValue);
                }
                $mapped[$dbFieldName] = $fieldValue;
            }

            if(in_array($dbFieldType, $nullables) && $fieldValue === "") {
                $mapped[$dbFieldName] = null;
            }
        }
        return $mapped;
    }

    private function getObjectAsArrayInSnakeCase(object $objectDto): array
    {
        $asArray = [];
        $reflection = new ReflectionObject($objectDto);
        $reflectionProperties = $reflection->getProperties();
        $reflectionProperties = array_map(fn ($item) => $item->getName(), $reflectionProperties);
        foreach ($reflectionProperties as $reflectionProperty) {
            $value = $objectDto->{$reflectionProperty}();
            $propertySnake = preg_replace("/([a-z])([A-Z])/", "$1_$2", $reflectionProperty);
            $propertySnake = strtolower($propertySnake);
            $asArray[$propertySnake] = $value;
        }
        return $asArray;
    }

    public function areAllPksPresent(array $pkValues): bool
    {
        if (!$pkValues) {
            return false;
        }
        $pkNames = array_keys($pkValues);
        foreach ($this->pks as $pkFieldName) {
            if (!in_array($pkFieldName, $pkNames)) {
                return false;
            }
            $value = $pkValues[$pkFieldName];
            if (!$value) {
                return false;
            }
        }
        return true;
    }
    public function addSysInsert(array &$request, string $idUser, string $platform = PlatformType::WEB): self
    {
        $request[EntityType::INSERT_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::INSERT_USER] = $idUser;
        $request[EntityType::INSERT_PLATFORM] = $platform;
        return $this;
    }

    public function addSysUpdate(array &$request, string $idUser, string $platform = PlatformType::WEB): self
    {
        $request[EntityType::UPDATE_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::UPDATE_USER] = $idUser;
        $request[EntityType::UPDATE_PLATFORM] = $platform;
        return $this;
    }

    public function addSysDelete(array &$request, string $updateDate, string $idUser, string $platform = PlatformType::WEB): self
    {
        $request[EntityType::DELETE_DATE] = date("Y-m-d H:i:s");
        $request[EntityType::DELETE_USER] = $idUser;
        $request[EntityType::DELETE_PLATFORM] = $platform;
        $request[EntityType::UPDATE_DATE] = $updateDate;
        return $this;
    }

}//AppEntity
