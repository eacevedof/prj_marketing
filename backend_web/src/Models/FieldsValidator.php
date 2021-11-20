<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Models\FieldsValidator 
 * @file FieldsValidator.php 1.0.0
 * @date 19-11-2021 20:00 SPAIN
 * @observations
 */
namespace App\Models;
use App\Enums\ModelType;

final class FieldsValidator
{
    private array $rules = [];
    private array $request;
    private AppModel $model;
    private array $errors = [];
    private array $skip = [];

    public function __construct(array $request, AppModel $model)
    {
        $this->request = $request;
        $this->model = $model;
    }
    
    private function _is_length($field): bool
    {
        $reqkey = $this->model->get_requestkey($field);
        $ilen = $this->model->get_length($field);
        $value = $this->request[$reqkey] ?? "";
        return (strlen($value)<=$ilen);
    }

    private function _is_datetime_ok(string $datetime): bool
    {
        return (date("Y-m-d H:i:s", strtotime($datetime)) == $datetime);
    }

    private function _is_date(string $field): bool
    {
        return in_array($this->model->get_type($field), [ModelType::DATE,ModelType::DATETIME]);
    }
    
    private function _is_type($field): bool
    {
        $reqkey = $this->model->get_requestkey($field);
        $type = $this->model->get_type($field);
        $value = $this->request[$reqkey] ?? null;

        switch ($type) {
            case ModelType::INT: return is_integer($value) || is_null($value);
            case ModelType::DECIMAL: return is_float($value) || is_null($value);
            case ModelType::DATE:
                return strtotime($value) || is_null($value) || $value==="";
            case ModelType::DATETIME:
                return $this->_is_datetime_ok($value) || is_null($value) || $value==="";
            case ModelType::STRING:
                return is_string($field) || is_null($value) || is_integer($value) || is_float($value);
        }

        return false;
    }

    private function _get_reqkeys(): array
    {
        return array_keys($this->request);
    }

    private function _check_rules(): void
    {
        foreach($this->rules as $rule) {
            $reqkey = $this->model->get_requestkey($field = $rule["field"]);
            $label = $this->model->get_label($field);
            $message = $rule["fn"]([
                "data" => $this->request,
                "field" => $field,
                "value" => $this->request[$reqkey] ?? null,
                "label" => $label
            ]);
            
            if ($message) {
                $this->_add_error(
                    $reqkey,
                    $rule["rule"],
                    $message,
                    $label);
            }
        }
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

            $field = $this->model->get_field($reqkey);
            if(!$field) {
                $this->_add_error(
                    $reqkey,
                    "unrecognized",
                    __("Unrecognized field"),
                    "");
                continue;
            }

            $label = $this->model->get_label($field);

            if (!($this->_is_length($field) || $this->_is_date($field))) {
                $ilen = $this->model->get_length($field);
                $ilenreq = strlen($this->request[$reqkey] ?? "");

                $this->_add_error(
                    $reqkey,
                    "length",
                    __("Max length allowed is {0} chars. {1} size is {2}",$ilen, $label, $ilenreq),
                    $label);
                continue;
            }

            if (!$this->_is_type($field)) {
                $type = $this->model->get_type($field);
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

}//FieldsValidator
