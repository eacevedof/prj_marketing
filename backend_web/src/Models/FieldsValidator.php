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

    public function add_rule(string $field, string $rule, callable $fn): self
    {
        $this->rules[] = ["field"=>$field, "rule"=>$rule, "fn"=>$fn];
        return $this;
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
            case Model::DATETIME:
                return $this->_is_datetime($value) || is_null($value) || $value==="";

        }

        return false;
    }

    public function get_errors(): array
    {
        $fields = $this->model->get_fieldnames();

        foreach ($fields as $field) {
            if (!$this->_is_length($field)) {
                $ilen = $this->model->get_length();
                $this->_add_error(
                    $field,
                    "length",
                    __("Max length allowed is {0}",$ilen),
                    $this->model->get_label($field));
                continue;
            }



        }

        return [];
    }


    private function _add_error(string $field, string $rule, string $message, string $label): self
    {
        $this->errors[] = [
            "field" => $field,
            "rule" => $rule,
            "label" => $label,
            "message" => $message,
        ];
    }

}//FieldsValidator
