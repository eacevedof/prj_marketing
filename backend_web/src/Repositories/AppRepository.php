<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\AppRepository
 * @file AppRepository.php 2.1.0
 * @date 28-06-2018 00:00 SPAIN
 * @observations
 */
namespace App\Repositories;
use App\Models\AppModel;
use TheFramework\Components\Db\ComponentCrud;
use TheFramework\Components\Db\ComponentMysql;

abstract class AppRepository
{
    protected AppModel $model;
    protected ComponentMysql $db;
    protected ComponentCrud $crud;
    protected string $table;

    public function set_db(ComponentMysql $db): self
    {
        $this->db = $db;
        return $this;
    }

    protected function _load_crud(): void
    {
        if (!$this->crud) {
            $this->crud = new ComponentCrud();
            $this->crud->set_table($this->table);
        }
    }


}//AppRepository
