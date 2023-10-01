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
    private array $calculatedFields = [];
    private array $joins = [
        "fields" => [],
        "on" => [],
    ];

    private function _getSanitizedStringForSearchValue(?string $searchText): ?string
    {
        if ($searchText === null) {
            return null;
        }
        $searchText = str_replace("\\", "\\\\", $searchText);
        $searchText = str_replace("'", "\'", $searchText);
        return $searchText;
    }

    private function _getJoinField(string $field): string
    {
        $key = array_search($field, $this->joins["fields"]);
        if ($key === false) {
            return "";
        }
        return $key;
    }

    private function _getCalculatedField(string $field): string
    {
        $key = array_search($field, $this->calculatedFields);
        if ($key === false) {
            return "m.$field";
        }
        return $key;
    }

    private function _getLikeCondition(string $field, string $value): string
    {
        $value = $this->_getSanitizedStringForSearchValue($value);
        $found = $this->_getJoinField($field);
        if (!$found) {
            $found = $this->_getCalculatedField($field);
        }
        return "$found LIKE '%$value%'";
    }

    private function _addJoinsToQueryBuilder(ComponentQB $qb): void
    {
        foreach ($this->joins["fields"] as $field => $alias) {
            $qb->add_getfield("$field as $alias");
        }

        foreach ($this->joins["on"] as $join) {
            $qb->add_join($join);
        }
    }

    private function _addCalculatedFieldToQueryBuilder(ComponentQB $qb): void
    {
        foreach ($this->calculatedFields as $calc => $field) {
            $qb->add_getfield("$calc as $field");
        }
    }

    private function _addSearchFilterToQueryBuilder(ComponentQB $qb, array $search): void
    {
        if (!$search) {
            return;
        }

        if ($fields = $search["fields"]) {
            foreach ($fields as $field => $value) {
                $qb->add_and($this->_getLikeCondition($field, $value));
            }
        }

        if ($limit = $search["limit"]) {
            $qb->set_limit($limit["length"], $limit["from"]);
        }

        if ($order = $search["order"]) {
            $field = $this->_getJoinField($order["field"]);
            if (!$field) {
                $field = $this->_getCalculatedField($order["field"]);
            }
            $qb->set_orderby([$field => "{$order["dir"]}"]);
        }

        if ($global = $search["global"]) {
            $or = [];
            foreach ($search["all"] as $field) {
                $or[] = $this->_getLikeCondition($field, $global);
            }
            $or = implode(" OR ", $or);
            $qb->add_and("($or)");
        }
    }
}//SearchRepoTrait
