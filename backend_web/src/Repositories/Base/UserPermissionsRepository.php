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
use App\Models\Base\UserPermissionModel;
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
         * @var UserPermissionModel
         */
        $this->model = MF::get("Base\UserPermissions");
        $this->_load_crud();
    }

    public function by_user(int $userid): array
    {
        $sql = $this->crud
            ->set_table("$this->table as m")
            ->set_getfields([

            ])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$userid")
            ->get_selectfrom()
        ;
        $ar = $this->db->query($sql);

        if(count($ar)>1) return [];
        return $ar[0] ?? [];
    }

}//ExampleRepository
