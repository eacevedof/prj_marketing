<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\App\ArrayRepository
 * @file ArrayRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Domain\Repositories\App;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\PicklistTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Shared\Infrastructure\Enums\AppArrayType as Types;
use App\Shared\Infrastructure\Enums\AppArrayType;

final class ArrayRepository extends AppRepository
{
    use PicklistTrait;

    private array $result = [];

    public function __construct()
    {
        $this->db = DbF::get_by_default();
    }

    public function get_promotion_types(?int $idowner=0): array
    {
        if(!$idowner) $idowner = 0;
        $type = Types::PROMOTION;
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_promotion_types")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("(m.id_owner=-1 OR m.id_owner=$idowner)")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function get_languages(): array
    {
        $type = Types::LANGUAGE;
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_languages")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function get_countries(): array
    {
        $type = Types::COUNTRY;
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_countries")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function exists(int $id, string $type): bool
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.exists_erp")
            ->set_table("app_array as m")
            ->set_getfields(["m.id"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id=$id")
            ->add_and("m.type='$type'")
            ->select()->sql()
        ;
        return (bool) $this->db->query($sql, 0, 0);
    }

    public function exists_erp(string $codeerp, string $type): int
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.exists_erp")
            ->set_table("app_array as m")
            ->set_getfields(["m.id"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.code_erp='$codeerp'")
            ->add_and("m.type='$type'")
            ->select()->sql()
        ;
        return (int) $this->db->query($sql, 0, 0);
    }

}//ArrayRepository
