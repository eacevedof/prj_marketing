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


final class ExampleRepository extends AppRepository
{
    public function __construct()
    {
        $this->_set_table();
    }

    public function get_all(): array
    {

        return $this->crud->get_selectfrom();
    }

}//ExampleRepository
