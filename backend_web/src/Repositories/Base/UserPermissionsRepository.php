<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\ExampleRepository 
 * @file ExampleRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Base;

use App\Models\Base\UserModel;
use App\Models\Base\UserPermissionsModel;
use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;
use App\Factories\ModelFactory as MF;

final class UserPermissionsRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_users_permissions";
        /**
         * @var UserPermissionsModel
         */
        //$this->model = MF::get("Base\UserPermissions");
        $this->_load_crud();
    }

    public function get_by_user(int $userid): array
    {
        $sql = $this->crud
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.id","m.json_rw"
            ])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$userid")
            ->get_selectfrom()
        ;
        $ar = $this->db->query($sql);
        return $ar[0] ?? [];
    }

}//ExampleRepository
