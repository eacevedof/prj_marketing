<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\Base\UserPreferencesRepository
 * @file UserPreferencesRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Repositories\Base;

use App\Repositories\AppRepository;
use App\Factories\DbFactory as DbF;

final class UserPreferencesRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user_preferences";
    }

    public function get_by_user(int $userid, string $prefkey=""): array
    {
        $crud = $this->_get_crud()
            ->set_comment("userpreferences.get_by_user(userid)")
            ->set_table("$this->table as m")
            ->set_getfields(["m.pref_key","m.pref_value"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$userid")
        ;
        if ($prefkey) $crud->add_and("m.pref_key='$prefkey'");
        return $this->db->query($crud->get_selectfrom());
    }
}//UserPreferencesRepository
