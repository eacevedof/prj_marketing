<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\Base\UserPreferencesRepository
 * @file UserPreferencesRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */
namespace App\Restrict\Users\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class UserPreferencesRepository extends AppRepository
{
    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "base_user_preferences";
    }

    public function get_by_user(int $iduser, string $prefkey=""): array
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_by_user(userid)")
            ->set_table("$this->table as m")
            ->set_getfields(["m.pref_key","m.pref_value"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
        ;
        if ($prefkey) $qb->add_and("m.pref_key='$prefkey'");
        return $this->db->query($qb->select()->sql());
    }
}//UserPreferencesRepository
