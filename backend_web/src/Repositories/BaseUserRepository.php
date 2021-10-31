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

use App\Factories\DbFactory;
use App\Models\BaseUserMoel;

final class BaseUserRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbFactory::get_by_default();
        $this->table = "app_example";
        $this->model = new BaseUserMoel();
        $this->_load_crud();
    }

    public function get_all(): array
    {
        $fields = array_keys($this->model->get_fields());
        $this->crud->set_getfields($fields);
        $this->crud->get_selectfrom();
        $sql = $this->crud->get_sql();
        return $this->db->query($sql);
    }



}//ExampleRepository
