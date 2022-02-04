<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\Base\UserPermissionsRepository
 * @file UserPermissionsRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class UserPermissionsRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user_permissions";
    }

    public function get_by_user(int $userid): array
    {
        $sql = $this->_get_qbuilder()
            ->set_comment("userpermission.get_by_user(userid)")
            ->set_table("$this->table as m")
            ->set_getfields(["m.json_rw"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$userid")
            ->select()->sql()
        ;
        $json = $this->db->query($sql, 0, 0);
        if(!$json) return [];
        return json_decode($json,1);
    }

}//ExampleRepository
