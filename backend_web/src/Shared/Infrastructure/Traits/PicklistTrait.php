<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Traits\PicklistTrait
 * @file PicklistTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Infrastructure\Traits;

trait PicklistTrait
{
    private function _getKeyValueArray(array $kv, bool $firstBlank = true): array
    {
        list($key, $value) = $kv;
        $picklist = [];
        if ($firstBlank) {
            $picklist[] = ["key" => "", "value" => __("Select an option")];
        }
        foreach ($this->result as $row) {
            $picklist[] = ["key" => $row[$key], "value" => $row[$value]];
        }

        return $picklist;
    }

}//PicklistTrait
