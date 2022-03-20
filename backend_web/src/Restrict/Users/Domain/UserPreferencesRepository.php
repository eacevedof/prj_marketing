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
            ->add_orderby("id", "DESC")
        ;
        return $this->db->query($qb->select()->sql());
    }

    public function get_by_id_and_user(int $id, int $iduser): int
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_by_id_and_user")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id = $id")
            ->add_and("m.id_user=$iduser")
        ;
        $r = $this->db->query($qb->select()->sql());
        return (int) ($r[0]["id"] ?? "");
    }

    public function get_id_by_id_and_user(int $id, int $iduser): array
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_id_by_id_and_user")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id = $id")
            ->add_and("m.id_user=$iduser")
        ;
        $r = $this->db->query($qb->select()->sql());
        return $r[0] ?? [];
    }

    public function get_value_by_user_and_key(int $iduser, string $prefkey): ?string
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.get_value_by_user_and_key")
            ->set_table("$this->table as m")
            ->set_getfields(["m.pref_value"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
            ->add_and("m.pref_key='$prefkey'");
        $r = $this->db->query($qb->select()->sql());
        return $r[0]["pref_value"] ?? null;
    }

    public function key_exists(int $iduser, string $prefkey): int
    {
        $qb = $this->_get_qbuilder()
            ->set_comment("userpreferences.key_exists")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_user=$iduser")
            ->add_and("m.pref_key='$prefkey'");
        $r = $this->db->query($qb->select()->sql());
        return (int) ($r[0]["id"] ?? 0);
    }

}//UserPreferencesRepository
