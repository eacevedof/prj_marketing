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

use App\Repositories\AppRepository;
use TheFramework\Components\Db\ComponentMysql;
use App\Models\ExampleModel;

final class ExampleRepository extends AppRepository
{
    public function __construct()
    {
        $this->table = "app_example";
        $this->model = new ExampleModel();
        $this->_get_crud();
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
