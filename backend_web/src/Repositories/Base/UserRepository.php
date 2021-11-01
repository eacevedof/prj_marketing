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
use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;
use App\Factories\ModelFactory as MF;

final class UserRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user";
        /**
         * @var UserModel
         */
        $this->model = MF::get("Base\User");
        $this->_load_crud();
    }


    public function by_email(string $email): array
    {
        $email = $this->_get_sanitized($email);
        $fields = $this->model->get_fields();
        $sql = $this->crud
                ->set_getfields($fields)
                ->add_and("email='$email'")
                ->get_selectfrom()
        ;
        $ar = $this->db->query($sql);
        return $ar[0] ?? [];
    }

}//ExampleRepository
