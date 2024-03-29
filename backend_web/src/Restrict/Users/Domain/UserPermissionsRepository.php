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
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "base_user_permissions";
    }

    public function getUserPermissionByIdUser(int $idUser): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("userpermission.get_by_user(userid)")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$idUser")
            ->select()->sql()
        ;
        return $this->query($sql)[0] ?? [];
    }
}
