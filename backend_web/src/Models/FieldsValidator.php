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
use App\Enums\Model;

final class FieldsValidator
{
    private array $rules = [];
    private array $data;
    private AppModel $model;
    private array $errors = [];

    public function __construct(array $data, AppModel $model)
    {
        $this->data = $data;
        $this->model = $model;
    }
    
    private function _is_length($field): bool
    {
        $postkey = $this->model->get_postkey($field);
        $ilen = $this->model->get_length($field);
        $value = $this->data[$postkey] ?? "";
        return (strlen($value)<=$ilen);
    }

    private function _is_datetime(string $datetime): bool
    {
        return (date("Y-m-d H:i:s", strtotime($datetime)) == $datetime);
    }
    
    private function _is_type($field): bool
    {
        $postkey = $this->model->get_postkey($field);
        $type = $this->model->get_type($field);
        $value = $this->data[$postkey] ?? null;

        switch ($type) {
            case Model::INT: return is_integer($value) || is_null($value);
            case Model::DECIMAL: return is_float($value) || is_null($value);
            case Model::DATE:
                return strtotime($value) || is_null($value) || $value==="";
            case Model::DATETIME:
                return $this->_is_datetime($value) || is_null($value) || $value==="";
            case Model::STRING:
                return is_string($field) || is_null($value) || is_integer($value) || is_float($value);
        }

        return false;
    }

    private function _get_fields(): array
    {
        return array_keys($this->data);
    }

    public function get_errors(): array
    {
        $fields = $this->_get_fields();

        foreach ($fields as $postfield) {
            $field = $this->model->get_field($postfield);
            if(!$field) {
                $this->_add_error(
                    $postfield,
                    "unrecognized",
                    __("Unrecognized field"),
                    "");
                continue;
            }

            $label = $this->model->get_label($field);

            if (!$this->_is_length($field)) {
                $ilen = $this->model->get_length();
                $this->_add_error(
                    $postfield,
                    "length",
                    __("Max length allowed is {0}",$ilen),
                    $label);
                continue;
            }

            if (!$this->_is_type($field)) {
                $type = $this->model->get_type($field);
                $this->_add_error(
                    $postfield,
                    "type",
                    __("Wrong datatype. Allowed: {0}",$type),
                    $label);
            }
        }

        if($this->errors) return $this->errors;

        $this->_check_rules();
        return $this->errors;
    }

    public function add_rule(string $field, string $rule, callable $fn): self
    {
        $this->rules[] = ["field"=>$field, "rule"=>$rule, "fn"=>$fn];
        return $this;
    }
    
    private function _check_rules(): void
    {
        foreach($this->rules as $rule) {
            $postfield = $this->model->get_postkey($field = $rule["field"]);
            $label = $this->model->get_label($field);
            $message = $rule["fn"]([
                "data" => $this->data,
                "field" => $field,
                "value" => $this->data[$postfield],
                "label" => $label
            ]);
            
            if ($message) {
                $this->_add_error(
                    $postfield,
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

}//FieldsValidator
