<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\ExampleRepository 
 * @file ExampleRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories;

use App\Factories\DbFactory as DbF;
use App\Factories\ModelFactory as MF;

final class UserRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user";
        $this->model = MF::get("Base\User");
        $this->_load_crud();
    }

    public function get_all(): array
    {
        $fields = array_keys($this->model->get_fields());
        $this->crud->set_getfields();
        $this->crud->get_selectfrom();
        $sql = $this->crud->get_sql();
        $ar = $this->db->query($sql);
        return $ar;
    }



}//ExampleRepository
