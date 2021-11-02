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
        $this->_load_crud();
    }

    public function by_email(string $email): array
    {
        $email = $this->_get_sanitized($email);
        $sql = $this->crud
                ->set_table("$this->table as m")
                ->set_getfields([
                    "m.id","m.email","m.secret","m.id_language",
                    "ar.code_erp as language"
                ])
                ->add_join("LEFT JOIN app_array ar ON m.id_language = ar.id AND ar.type='language'")
                ->add_and("m.is_enabled=1")
                ->add_and("m.delete_date IS NULL")
                ->add_and("m.email='$email'")
                ->get_selectfrom()
        ;
        $ar = $this->db->query($sql);
        if(count($ar)>1) return [];
        return $ar[0] ?? [];
    }

}//ExampleRepository
