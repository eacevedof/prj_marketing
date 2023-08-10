<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Entities
 * @file FieldsValidator.php 1.0.0
 * @date 19-11-2021 20:00 SPAIN
 * @observations
 */

namespace App\Shared\Domain\Entities;

use App\Shared\Domain\Enums\EntityType;
use App\Shared\Infrastructure\Factories\ComponentFactory as CF;
use App\Shared\Infrastructure\Components\Formatter\TextComponent;

final class FieldsValidator
{
    private array $rules = [];
    private array $requestArray;
    private ?AppEntity $appEntity;
    private array $errors = [];
    private array $skippAbleFields = [];

    public function __construct(array | object $request, ?AppEntity $entity = null)
    {
        $this->requestArray = is_object($request)
            ? CF::getInstanceOf(TextComponent::class)->getObjectAsArrayInSnakeCase($request)
            : $request;
        $this->appEntity = $entity;
    }

    private function _isLengthOk(string $fieldName): bool
    {
        $requestKey = $this->appEntity->getRequestKeyByFieldName($fieldName);
        $fieldLength = $this->appEntity->getLength($fieldName);
        $value = $this->requestArray[$requestKey] ?? "";
        return (strlen($value) <= $fieldLength);
    }

    private function _isDatetimeOk(string $datetime): bool
    {
        $datetime = str_replace("T", " ", $datetime);
        return (date("Y-m-d H:i:s", strtotime($datetime)) == $datetime);
    }

    private function _isFieldTypeDate(string $fieldName): bool
    {
        return in_array(
            $this->appEntity->getTypeByFieldName($fieldName),
            [EntityType::DATE, EntityType::DATETIME]
        );
    }

    private function _isFieldTypeOk(string $fieldName): bool
    {
        $requestKey = $this->appEntity->getRequestKeyByFieldName($fieldName);
        $requestValue = $this->requestArray[$requestKey] ?? null;
        if ($this->_isEmpty($requestValue)) {
            return true;
        }

        $fieldType = $this->appEntity->getTypeByFieldName($fieldName);
        switch ($fieldType) {
            case EntityType::INT: return is_numeric($requestValue);
            case EntityType::DECIMAL: return is_float($requestValue) || is_numeric($requestValue);
            case EntityType::DATE: return strtotime($requestValue);
                //ejemplo datetime: 2022-01-22 00:00:00
            case EntityType::DATETIME: return $this->_isDatetimeOk($requestValue);
            case EntityType::STRING: return is_string($requestValue);
        }
        return false;
    }

    private function _isEmpty(?string $val): bool
    {
        return $val === "" || is_null($val);
    }

    private function _getKeysFromArrayRequest(): array
    {
        return array_keys($this->requestArray);
    }

    private function _evaluateAllRulesAndAppendErrors(): void
    {
        foreach($this->rules as $rule) {
            $field = $rule["field"];
            $requestKey = $field;
            $label = $this->requestArray["label-$field"] ?? "";

            if ($this->appEntity) {
                $requestKey = $this->appEntity->getRequestKeyByFieldName($field);
                $label = $this->appEntity->getLabel($field);
            }

            $message = $rule["fn"]([
                "data" => $this->requestArray,
                "field" => $field,
                "value" => $this->requestArray[$requestKey] ?? null,
                "label" => $label
            ]);

            if ($message) {
                $this->_addError($requestKey, $rule["rule"], $message, $label);
            }

        }//foreach
    }

    private function _addError(string $fieldName, string $ruleName, string $message, string $label): void
    {
        $this->errors[] = [
            "field" => $fieldName,
            "rule" => $ruleName,
            "label" => $label,
            "message" => $message,
        ];
    }

    private function _isOperationField(string $requestKey): bool
    {
        return (str_starts_with($requestKey, "_"));
    }

    private function _isFieldSkippAble(string $requestKey): bool
    {
        return in_array($requestKey, $this->skippAbleFields);
    }

    private function _checkEntityFields(array $requestKeys): void
    {
        if (!$this->appEntity) {
            return;
        }

        foreach ($requestKeys as $requestKey) {
            if ($this->_isOperationField($requestKey) || $this->_isFieldSkippAble($requestKey)) {
                continue;
            }

            $field = $this->appEntity->getFieldNamedByRequestKey($requestKey);
            if (!$field) {
                $this->_addError(
                    $requestKey,
                    "unrecognized",
                    __("Unrecognized field"),
                    ""
                );
                continue;
            }

            $label = $this->appEntity->getLabel($field);
            if (!($this->_isLengthOk($field) || $this->_isFieldTypeDate($field))) {
                $fieldLength = $this->appEntity->getLength($field);
                $requestValueLength = strlen($this->requestArray[$requestKey] ?? "");

                $this->_addError(
                    $requestKey,
                    "length",
                    __("Max length allowed is {0} chars. {1} size is {2}", $fieldLength, $label, $requestValueLength),
                    $label
                );
                continue;
            }

            if (!$this->_isFieldTypeOk($field)) {
                $type = $this->appEntity->getTypeByFieldName($field);
                $this->_addError(
                    $requestKey,
                    "type",
                    __("Wrong datatype. Allowed: {0}", $type),
                    $label
                );
            }
        }
    }

    public function getErrors(): array
    {
        $requestKeys = $this->_getKeysFromArrayRequest();
        $this->_checkEntityFields($requestKeys);
        if ($this->errors) {
            return $this->errors;
        }
        $this->_evaluateAllRulesAndAppendErrors();
        return $this->errors;
    }

    public function addSkipableField(string $fieldName): self
    {
        $this->skippAbleFields[] = $fieldName;
        return $this;
    }

    public function addRule(string $fieldName, string $ruleName, callable $fn): self
    {
        $this->rules[] = ["field" => $fieldName, "rule" => $ruleName, "fn" => $fn];
        return $this;
    }

    public function getSkippAbleFields(): array
    {
        return $this->skippAbleFields;
    }
}
