<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Queries\Domain\QueryRepository
 * @file QueryRepository.php v1.0.0
 * @date %DATE% SPAIN
 */
namespace App\Restrict\Queries\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use TheFramework\Components\Db\ComponentQB;

final class QueryRepository extends AppRepository
{

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "app_query";
    }

    public function get_by_uuid_and_iduser(string $uuid, int $iduser, array $fields=[]): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("app_query.get_by_uuid_and_iduser")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.uuid='$uuid'")
            ->add_and("m.insert_user=$iduser")
        ;
        if ($fields) $sql->set_getfields($fields);
        $sql = $sql->select()->sql();
        $r = $this->db->query($sql);
        return $r[0] ?? [];
    }

}//QueryRepository
