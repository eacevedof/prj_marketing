<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\ViewTrait
 * @file ViewTrait.php 1.0.0
 * @date 30-10-2021 15:00 SPAIN
 * @observations
 * @tags: #ui
 */
namespace App\Shared\Infrastructure\Traits;

use TheFramework\Components\Db\ComponentQB;

trait SearchRepoTrait
{
    private array $joins = [
        "fields" => [],
        "on" => [],
    ];

    private function get_sanitized(?string $strval): ?string
    {
        if($strval===null) return null;
        $strval = str_replace("\\","\\\\",$strval);
        $strval = str_replace("'","\'",$strval);
        return $strval;
    }//get_sanitized

    private function _get_join_field(string $field): string
    {
        $key = array_search($field, $this->joins["fields"]);
        if ($key===false) return "m.$field";
        return $key;
    }

    private function _get_condition(string $field, string $value): string
    {
        $value = $this->get_sanitized($value);
        $field = $this->_get_join_field($field);
        return "$field LIKE '%$value%'";
    }

    private function _add_joins(ComponentQB $qb): void
    {
        foreach ($this->joins["fields"] as $field => $alias)
            $qb->add_getfield("$field as $alias");

        foreach ($this->joins["on"] as $join)
            $qb->add_join($join);
    }

    private function _add_search_filter(ComponentQB $qb, array $search): void
    {
        if(!$search) return;

        if($fields = $search["fields"])
            foreach ($fields as $field => $value )
                $qb->add_and($this->_get_condition($field, $value));

        if($limit = $search["limit"])
            $qb->set_limit($limit["length"], $limit["from"]);

        if($order = $search["order"]) {
            $field = $this->_get_join_field($order["field"]);
            $qb->set_orderby([$field => "{$order["dir"]}"]);
        }

        if($global = $search["global"]) {
            $or = [];
            foreach ($search["all"] as $field)
                $or[] = $this->_get_condition($field, $global);
            $or = implode(" OR ",$or);
            $qb->add_and("($or)");
        }
    }
}//SearchRepoTrait
