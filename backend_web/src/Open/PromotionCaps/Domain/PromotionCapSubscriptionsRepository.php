<?php

namespace App\Open\PromotionCaps\Domain;

use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Shared\Infrastructure\Factories\{DbFactory as DbF, RepositoryFactory as RF};

final class PromotionCapSubscriptionsRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_promotioncap_subscriptions";
    }

    public function get_info(string $uuid): array
    {
        $uuid = $this->_getSanitizedString($uuid);
        $sql = $this->_getQueryBuilderInstance()
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
        $this->mapFieldsToInt($r, ["id", "id_owner", "id_promotion", "id_promouser"]);
        if (!$r) {
            return [];
        }
        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);
        return array_merge($r, $sysData);
    }

}
