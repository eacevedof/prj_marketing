<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\Base\ArrayRepository
 * @file ArrayRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Domain\Repositories\Base;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\PicklistTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class ArrayRepository extends AppRepository
{
    use PicklistTrait;

    private array $result = [];

    public function __construct()
    {
        $this->db = DbF::get_by_default();
    }

    public function get_profiles(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_profiles")
            ->set_table("base_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='profile'")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

}//ArrayRepository
