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

use App\Enums\ModelType;
use App\Enums\PlatformType;

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
        return $this->fields[$field][ModelType::REQUEST_KEY] ?? $field;
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
             return $array[ModelType::REQUEST_KEY] ?? "";
         }, $this->fields);

         return in_array($field, $fields);
    }

    public function get_field(string $postfield): string
    {
        foreach ($this->fields as $field => $array) {
            if($array[ModelType::REQUEST_KEY] === $postfield)
                return $field;
        }
        return "";
    }

    public function map_post(array $post): array
    {
        $postfields = array_keys($post);
        $mapped = [];
        foreach ($postfields as $postfield) {
            $dbfield = $this->get_field($postfield);
            if($dbfield) {
                $mapped[$dbfield] = $post[$postfield];
            }
        }
        return $mapped;
    }

    public function add_sysinsert(array &$post, string $user, string $platform=PlatformType::WEB): self
    {
        $post[ModelType::INSERT_DATE] = date("Y-m-d H:i:s");
        $post[ModelType::INSERT_USER] = $user;
        $post[ModelType::INSERT_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysupdate(array &$post, string $user, string $platform=PlatformType::WEB): self
    {
        $post[ModelType::UPDATE_DATE] = date("Y-m-d H:i:s");
        $post[ModelType::UPDATE_USER] = $user;
        $post[ModelType::UPDATE_PLATFORM] = $platform;
        return $this;
    }

    public function add_sysdelete(array &$post, string $user, string $platform=PlatformType::WEB): self
    {
        $post[ModelType::DELETE_DATE] = date("Y-m-d H:i:s");
        $post[ModelType::DELETE_USER] = $user;
        $post[ModelType::DELETE_PLATFORM] = $platform;
        return $this;
    }
}//AppModel
