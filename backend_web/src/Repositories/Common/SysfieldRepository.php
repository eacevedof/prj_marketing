<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\Common\SysfieldRepository
 * @file SysfieldRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Common;

use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;

final class SysfieldRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
    }
    
    private function _get_user(string $userid): string
    {
        if (!$userid) return "";
        $id = (int) $userid;
        $sql = $this->_get_crud()
            ->set_table("base_user as m")
            ->set_getfields(["m.description"])
            ->add_and("m.id=$id")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        return $r[0]["description"] ?? "";
    }

    private function _get_platform(string $platformid): string
    {
        if (!$platformid) return "";
        $id = (int) $platformid;
        $sql = $this->_get_crud()
            ->set_table("base_array as m")
            ->set_getfields(["m.description"])
            ->add_and("m.id=$id")
            ->add_and("m.type='platform'")
            ->get_selectfrom()
        ;
        $r = $this->db->query($sql);
        return $r[0]["description"] ?? "";
    }
    
    public function get_sysdata(array $row): array
    {
        if (!$row) return [
            "insert_user"=>"", "insert_platform"=>"",
            "update_user"=>"", "update_platform"=>"",
            "delete_user"=>"", "delete_platform"=>"",
            ];

        return [
            "insert_user" => $this->_get_user($row["insert_user"] ?? ""),
            "insert_platform" => $this->_get_platform($row["insert_user"] ?? ""),

            "update_user" => $this->_get_user($row["update_user"] ?? ""),
            "update_platform" => $this->_get_platform($row["update_user"] ?? ""),

            "delete_user" => $this->_get_user($row["delete_user"] ?? ""),
            "delete_platform" => $this->_get_platform($row["delete_platform"] ?? ""),
        ];
    }
}//ExampleRepository
