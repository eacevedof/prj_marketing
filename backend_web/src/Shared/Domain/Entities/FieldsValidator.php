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

final class FieldsValidator
{
    private array $rules = [];
    private array $request;
    private AppEntity $entity;
    private array $errors = [];
    private array $skip = [];

    public function __construct(array $request, AppEntity $entity)
    {
        $this->request = $request;
        $this->entity = $entity;
    }
    
    private function _is_length($field): bool
    {
        $reqkey = $this->entity->get_requestkey($field);
        $ilen = $this->entity->get_length($field);
        $value = $this->request[$reqkey] ?? "";
        return (strlen($value)<=$ilen);
    }

    private function _is_datetime_ok(string $datetime): bool
    {
        $datetime = str_replace("T"," ", $datetime);
        return (date("Y-m-d H:i:s", strtotime($datetime)) == $datetime);
    }

    private function _is_date(string $field): bool
    {
        return in_array($this->entity->get_type($field), [EntityType::DATE, EntityType::DATETIME]);
    }
    
    private function _is_type($field): bool
    {
        $reqkey = $this->entity->get_requestkey($field);
        $type = $this->entity->get_type($field);
        $value = $this->request[$reqkey] ?? null;

        if ($this->_is_empty($value)) return true;

        switch ($type) {
            case EntityType::INT: return is_numeric($value);
            case EntityType::DECIMAL: return is_float($value) || is_numeric($value);
            case EntityType::DATE: return strtotime($value);
            //ejemplo datetime: 2022-01-22 00:00:00
            case EntityType::DATETIME: return $this->_is_datetime_ok($value);
            case EntityType::STRING: return is_string($field) || is_numeric($value) || is_float($value);
        }
        return false;
    }

    private function _is_empty(?string $val): bool { return $val==="" || is_null($val);}

    private function _get_reqkeys(): array
    {
        return array_keys($this->request);
    }

    private function _check_rules(): void
    {
        foreach($this->rules as $rule) {
            $reqkey = $this->entity->get_requestkey($field = $rule["field"]);
            $label = $this->entity->get_label($field);
            $message = $rule["fn"]([
                "data" => $this->request,
                "field" => $field,
                "value" => $this->request[$reqkey] ?? null,
                "label" => $label
            ]);
            
            if ($message)
                $this->_add_error($reqkey, $rule["rule"], $message, $label);

        }//foreach
    }

    private function _add_error(string $field, string $rule, string $message, string $label): self
    {
        $this->errors[] = [
            "field" => $field,
            "rule" => $rule,
            "label" => $label,
            "message" => $message,
        ];
        return $this;
    }

    private function _is_operation(string $key): bool
    {
        return (substr($key,0,1)=="_");
    }

    private function _in_skip(string $key): bool
    {
        return in_array($key,$this->skip);
    }

    public function get_errors(): array
    {
        $reqkeys = $this->_get_reqkeys();

        foreach ($reqkeys as $reqkey) {
            if($this->_is_operation($reqkey) || $this->_in_skip($reqkey))
                continue;

            $field = $this->entity->get_field($reqkey);
            if(!$field) {
                $this->_add_error(
                    $reqkey,
                    "unrecognized",
                    __("Unrecognized field"),
                    "");
                continue;
            }

            $label = $this->entity->get_label($field);

            if (!($this->_is_length($field) || $this->_is_date($field))) {
                $ilen = $this->entity->get_length($field);
                $ilenreq = strlen($this->request[$reqkey] ?? "");

                $this->_add_error(
                    $reqkey,
                    "length",
                    __("Max length allowed is {0} chars. {1} size is {2}",$ilen, $label, $ilenreq),
                    $label);
                continue;
            }

            if (!$this->_is_type($field)) {
                $type = $this->entity->get_type($field);
                $this->_add_error(
                    $reqkey,
                    "type",
                    __("Wrong datatype. Allowed: {0}",$type),
                    $label);
            }
        }

        if($this->errors) return $this->errors;

        $this->_check_rules();
        return $this->errors;
    }

    public function add_skip(string $field): self
    {
        $this->skip[] = $field;
        return $this;
    }

    public function add_rule(string $field, string $rule, callable $fn): self
    {
        $this->rules[] = ["field"=>$field, "rule"=>$rule, "fn"=>$fn];
        return $this;
    }

    public function get_skip(): array
    {
        return $this->skip;
    }

}//FieldsValidator
