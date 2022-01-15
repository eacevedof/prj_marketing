<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\App\PicklistRepository
 * @file PicklistRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\App;

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
        if ($blank) $picklist[] = ["key" => "", "value"=>__("Select an option")];
        foreach ($this->result as $row)
            $picklist[] = ["key"=>$row[$key],"value"=>$row[$value]];

        return $picklist;
    }

    public function get_languages(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_languages")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='language'")
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
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_countries")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='country'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
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
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

    public function get_users(): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("picklist.get_users")
            ->set_table("base_user as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            //->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->db->query($sql);
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
        $this->result = $this->db->query($sql);
        return $this->_get_associative(["id","description"]);
    }

}//PicklistRepository
