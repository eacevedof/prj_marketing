<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\App\PicklistRepository
 * @file PicklistRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Shared\Domain\Repositories\App;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\PicklistTrait;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class PicklistRepository extends AppRepository
{
    use PicklistTrait;

    private array $result = [];

    public function __construct()
    {
        $this->db = DbF::get_by_default();
    }

    public function get_users(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_users")
            ->set_table("base_user as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function get_users_by_profile(string $profileid): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_users_by_profile(profileid)")
            ->set_table("base_user as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_profile=$profileid")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function get_business_owners(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_business_owners")
            ->set_table("base_user as m")
            ->set_getfields(["m.id","m.description"])
            ->add_join("INNER JOIN app_business_data bd ON bd.id_user=m.id")
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("TRIM(COALESCE(bd.slug,''))!=''")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        return $this->_get_associative(["id","description"]);
    }

}//PicklistRepository
