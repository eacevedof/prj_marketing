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

    public function get_by_user(int $iduser): array
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_by_user")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
        ;
        return $this->db->query($qb->select()->sql());
    }

    public function get_value_by_user_and_key(int $iduser, string $prefkey): string
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_value_by_user_and_key")
            ->set_table("$this->table as m")
            ->set_getfields(["m.pref_value"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
            ->add_and("m.pref_key='$prefkey'");
        $r = $this->db->query($qb->select()->sql());
        return $r[0][0] ?? "";
    }
}//UserPreferencesRepository
