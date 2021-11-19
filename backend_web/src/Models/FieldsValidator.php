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

    }

    public function get_errors(): array
    {
        $fields = $this->model->get_fieldnames();

        foreach ($fields as $field) {
            if (!)
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
