<?php
/**
 * @author Module Builder
 * @link eduardoaf.com
 * @name App\Restrict\Promotions\Domain\PromotionUiRepository
 * @file PromotionUiRepository.php v1.0.0
 * @date %DATE% SPAIN
 */

namespace App\Restrict\Promotions\Domain;

use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Shared\Infrastructure\Helpers\PromotionUiHelper;
use App\Shared\Domain\Repositories\Common\SysFieldRepository;
use App\Shared\Infrastructure\Factories\{DbFactory as DbF, RepositoryFactory as RF};

final class PromotionUiRepository extends AppRepository
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_promotion_ui";
        $this->joins = [
            "fields" => [
                "u2.description"  => "e_deletedby",
                //"ar1.description" => "e_language",
            ],
            "on" => [
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                //"LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'",
            ]
        ];
    }

    private function _addConditionByAuthService(ComponentQB $qb): void
    {
        if (!$this->authService->getAuthUserArray()) {
            $qb->add_and("1 = 0");
            return;
        }

        if ($this->authService->isAuthUserRoot()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        //como no es root no puede ver borrados o desactivados
        $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");

        $authUser = $this->authService->getAuthUserArray();
        if ($this->authService->isAuthUserBusinessOwner()) {
            $qb->add_andoper("m.id_owner", $authUser["id"]);
            return;
        }

        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $authUser["id_parent"];
            $qb->add_andoper("m.id_owner", $idParent);
        }
    }

    public function search(array $search): array
    {
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("promotion_ui.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.id_owner",
                "m.code_erp",
                "m.description",
                "m.id_promotion",
                "m.input_email",
                "m.pos_email",
                "m.input_name1",
                "m.pos_name1",
                "m.input_name2",
                "m.pos_name2",
                "m.input_language",
                "m.pos_language",
                "m.input_country",
                "m.pos_country",
                "m.input_phone1",
                "m.pos_phone1",
                "m.input_birthdate",
                "m.pos_birthdate",
                "m.input_gender",
                "m.pos_gender",
                "m.input_address",
                "m.pos_address",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id" => "DESC"])
        ;
        $this->_addJoinsToQueryBuilder($qb);
        $this->_addSearchFilterToQueryBuilder($qb, $search);
        $this->_addConditionByAuthService($qb);

        $sql = $qb->select()->sql();
        $sqlCount = $qb->sqlcount();
        $r = $this->getQueryWithCount($sqlCount, $sql);
        return $r;
    }

    public function getPromotionUiInfoByPromotionUiUuid(string $uuid): array
    {
        $uuid = $this->_getSanitizedString($uuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotion_ui.get_info(uuid)")
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
                "m.input_email",
                "m.pos_email",
                "m.input_name1",
                "m.pos_name1",
                "m.input_name2",
                "m.pos_name2",
                "m.input_language",
                "m.pos_language",
                "m.input_country",
                "m.pos_country",
                "m.input_phone1",
                "m.pos_phone1",
                "m.input_birthdate",
                "m.pos_birthdate",
                "m.input_gender",
                "m.pos_gender",
                "m.input_address",
                "m.pos_address"
            ])
            //->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id AND ar1.type='language'")
            ->add_and("m.uuid='$uuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if (!$r) {
            return [];
        }

        $sysData = RF::getInstanceOf(SysFieldRepository::class)->getSysDataByRowData($r = $r[0]);

        return array_merge($r, $sysData);
    }

    public function setAuthService(AuthService $authService): self
    {
        $this->authService = $authService;
        return $this;
    }

    public function getPromotionUiByIdPromotion(int $idPromotion, array $fields = []): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("promotionuirepository.get_by_promotion")
            ->set_table("$this->table as m")
            ->set_getfields(["m.*"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id_promotion=$idPromotion")
        ;
        if ($fields) {
            $sql->set_getfields($fields);
        }
        $sql = $sql->select()->sql();

        $r = $this->query($sql);
        $this->mapFieldsToInt(
            $r,
            [
                "id", "id_owner", "id_promotion", "pos_email", "pos_name1", "pos_name2", "input_language", "pos_language",
                "input_country", "pos_country", "pos_phone1", "pos_birthdate", "input_gender"
            ]
        );
        return $r[0] ?? [];
    }

    public function getActiveFieldsByIdPromotion(int $idPromotion): array
    {
        $promotionUi = $this->getPromotionUiByIdPromotion($idPromotion, [
            "input_email","pos_email","input_name1","pos_name1","input_name2","pos_name2","input_language","pos_language",
            "input_country","pos_country","input_phone1","pos_phone1","input_birthdate","pos_birthdate","input_gender",
            "pos_gender","input_address","pos_address", "input_is_mailing", "pos_is_mailing", "input_is_terms", "pos_is_terms"
        ]);
        return PromotionUiHelper::fromPrimitives($promotionUi)->getInputs();
    }
}
