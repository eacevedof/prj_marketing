<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\PicklistRepository 
 * @file PicklistRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Base;

use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;

final class PicklistRepository extends AppRepository
{
    private array $result = [];

    public function __construct()
    {
        $this->db = DbF::get_by_default();
    }

    private function _get_associative(array $kv, bool $blank=true): array
    {
        list($key,$value) = $kv;
        $picklist = [];
        if ($blank) $picklist[""] = __("Select an option");
        foreach ($this->result as $row)
            $picklist[$row[$key]] = $row[$value];

        return $picklist;
    }

    public function get_languages(): array
    {
        $sql = $this->_get_crud()
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.delete_date IS NULL")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->get_selectfrom()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

}//ExampleRepository
