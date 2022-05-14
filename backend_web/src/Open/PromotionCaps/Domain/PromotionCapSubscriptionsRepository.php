<?php
namespace App\Open\PromotionCaps\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Factories\RepositoryFactory as RF;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\Common\SysfieldRepository;
use TheFramework\Components\Db\ComponentQB;

final class PromotionCapSubscriptionsRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $auth = null;

    public function __construct()
    {
        $this->db = DbF::get_by_default();
        $this->table = "app_promotioncap_subscriptions";
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_get_sanitized($uuid);
        $sql = $this->_get_qbuilder()
            ->set_comment("promotioncap_subscriptions.get_info(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.insert_user",
                "m.insert_date",
                "m.update_user",
                "m.update_date",
                "m.delete_user",
                "m.delete_date",
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.id_promotion",
                "m.id_promouser",
                "m.date_subscription",
                "m.date_confirm",
                "m.date_execution",
                "m.code_execution",
                "m.exec_user",
                "m.subs_status",
                "m.remote_ip",
                "m.notes"
            ])
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if (!$r) return [];
        $sysdata = RF::get(SysfieldRepository::class)->get_sysdata($r = $r[0]);
        return array_merge($r, $sysdata);
    }

}
