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

    public function get_fields(){return array_keys($this->fields);}
    public function get_pks(){return $this->pks;}

    public function validate(array $post): array
    {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            if(!isset($post[$field])) continue;

        }
        return [];
    }
}//AppModel
