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
        return $this->fields[$field]["in_post"] ?? $field;
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
             return $array["in_post"] ?? "";
         }, $this->fields);

         return in_array($field, $fields);
    }

    public function get_field(string $postfield): string
    {
        foreach ($this->fields as $field => $array) {
            if($array["in_post"] === $postfield)
                return $field;
        }
        return "";
    }
}//AppModel
