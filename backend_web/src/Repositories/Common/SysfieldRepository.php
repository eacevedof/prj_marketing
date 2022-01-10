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
        $sql = $this->_get_qb()
            ->set_comment("sysfield._get_user(userid)")
            ->set_table("base_user as m")
            ->set_getfields(["m.description"])
            ->add_and("m.id=$id")
            ->select()
        ;
        $r = $this->db->query($sql);
        return $r[0]["description"] ?? "";
    }

    private function _get_platform(string $platformid): string
    {
        if (!$platformid) return "";
        $sql = $this->_get_qb()
            ->set_comment("sysfield._get_platform(platformid)")
            ->set_table("base_array as m")
            ->set_getfields(["m.description"])
            ->add_and("m.code_erp=$platformid")
            ->add_and("m.type='platform'")
            ->select()
        ;
        $r = $this->db->query($sql);
        return $r[0]["description"] ?? "";
    }
    
    public function get_sysdata(array $row): array
    {
        $sys = [];
        if (isset($row["insert_user"])) $sys["insert_user"] = "";
        if (isset($row["insert_platform"])) $sys["insert_platform"] = "";
        if (isset($row["update_user"])) $sys["update_user"] = "";
        if (isset($row["update_platform"])) $sys["update_platform"] = "";
        if (isset($row["delete_user"])) $sys["delete_user"] = "";
        if (isset($row["delete_platform"])) $sys["delete_platform"] = "";

        if (!$sys) return [];

        foreach ($sys as $key=>$v) {
            if(strstr($key,"_user"))
                $sys[$key] = $this->_get_user($row[$key]);
            else
                $sys[$key] = $this->_get_platform($row[$key]);
        }

        return $sys;
    }
}//ExampleRepository
