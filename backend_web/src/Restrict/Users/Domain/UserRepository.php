<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Repositories\UserRepository
 * @file UserRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Restrict\Users\Domain;

use App\Shared\Infrastructure\Bus\EventBus;
use TheFramework\Components\Db\ComponentQB;
use App\Restrict\Auth\Application\AuthService;
use App\Shared\Domain\Bus\Event\IEventDispatcher;
use App\Shared\Domain\Repositories\AppRepository;
use App\Restrict\Users\Domain\Enums\UserProfileType;
use App\Shared\Infrastructure\Traits\SearchRepoTrait;
use App\Restrict\Queries\Domain\Events\QueryWasCreatedEvent;
use App\Shared\Infrastructure\Components\Hierarchy\HierarchyComponent;
use App\Shared\Infrastructure\Factories\{ComponentFactory as CF, DbFactory as DbF};

final class UserRepository extends AppRepository implements IEventDispatcher
{
    use SearchRepoTrait;

    private ?AuthService $authService = null;

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "base_user";
        $this->joins = [
            "fields" => [
                //"u1.description"  => "e_parent",
                "COALESCE(CONCAT(bd.business_name,' (',u1.description,')'),u1.description)" => "e_parent",
                "u2.description"  => "e_deletedby",
                "ar1.description" => "e_language",
                "ar2.description" => "e_profile",
                "ar3.description" => "e_country",
            ],
            "on" => [
                "LEFT JOIN base_user u1 ON m.id_parent = u1.id",
                "LEFT JOIN app_business_data bd ON m.id_parent = bd.id_user AND m.id_parent = u1.id",
                "LEFT JOIN base_user u2 ON m.delete_user = u2.id",
                "LEFT JOIN app_array ar1 ON m.id_language = ar1.id_pk AND ar1.type='language'",
                "LEFT JOIN base_array ar2 ON m.id_profile = ar2.id_pk AND ar2.type='profile'",
                "LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'",
            ]
        ];
    }

    private function _addAuthUserConditionToQueryBuilder(ComponentQB $qb): void
    {
        if (!$this->authService->isAuthUserRoot()) {
            $qb->add_and("m.is_enabled=1")->add_and("m.delete_date IS NULL");
        }

        if ($this->authService->isAuthUserRoot()) {
            $qb->add_getfield("m.delete_user")
                ->add_getfield("m.insert_date")
                ->add_getfield("m.insert_user");
            return;
        }

        $user = $this->authService->getAuthUserArray();
        if ($this->authService->hasAuthUserBusinessManagerProfile()) {
            $idParent = $user["id_parent"];
            $childs = $this->getChildrenIdsByIdUser($idParent);
            $childs = array_column($childs, "id");
            $qb->add_in("m.id", $childs);
            $qb->add_and("m.delete_date IS NULL");
            return;
        }

        if ($this->authService->isAuthUserBusinessOwner()) {
            $childs = $this->getChildrenIdsByIdUser($user["id"]);
            $childs = array_column($childs, "id");
            $childs[] = $user["id"];
            $qb->add_and("m.delete_date IS NULL");
            $qb->add_in("m.id", $childs);
        }
    }

    private function _dispatchQueryWasCreatedEvent(array $payload): void
    {
        EventBus::instance()->publish(...[
            QueryWasCreatedEvent::fromPrimitives(-1, $payload)
        ]);
    }

    public function search(array $search): array
    {
        $qb = $this->_getQueryBuilderInstance()
            ->set_comment("user.search")
            ->set_table("$this->table as m")
            ->calcfoundrows()
            ->set_getfields([
                "m.id",
                "m.uuid",
                "m.address",
                "m.birthdate",
                "m.date_validated",
                "m.description",
                "m.email",
                "m.fullname",
                "m.id_country",
                "m.id_gender",
                "m.id_language",
                "m.id_parent",
                "m.id_profile",
                "m.secret",
                "m.phone",
                "m.delete_date"
            ])
            ->set_limit(25, 0)
            ->set_orderby(["m.id" => "DESC"])
        ;
        $this->_addJoinsToQueryBuilder($qb);
        $this->_addSearchFilterToQueryBuilder($qb, $search);
        $this->_addAuthUserConditionToQueryBuilder($qb);

        $sql = $qb->select()->sql();
        $sqlCount = $qb->sqlcount();
        $r = $this->getQueryWithCount($sqlCount, $sql);
        $this->_dispatchQueryWasCreatedEvent([
            "uuid" => $md5 = md5($sql)."-".uniqid(),
            "description" => "read:search",
            "query" => $sql,
            "total" => $r["total"],
            "module" => "users",
        ]);

        $r["req_uuid"] = $md5;
        return $r;
    }

    public function getUserByEmail(string $email): array
    {
        $email = $this->_getSanitizedString($email);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("user.get_by_email")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.id", "m.fullname","m.description", "m.email", "m.secret", "m.id_language", "m.id_profile",
                "m.uuid", "m.id_parent",
                "ar1.code_erp as e_language"
            ])
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id_pk AND ar1.type='language'")
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.email='$email'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        if(count($r) > 1 || !$r) {
            return [];
        }

        $this->mapFieldsToInt($r, ["id", "id_language", "id_profile", "id_parent"]);
        return $r[0];
    }

    public function getUserIdByEmail(string $email): int
    {
        $email = $this->_getSanitizedString($email);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("user.email_exists")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.email='$email'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id"]);
        return $r[0]["id"] ?? 0;
    }

    public function getUserInfoByUserUuid(string $userUuid): array
    {
        $userUuid = $this->_getSanitizedString($userUuid);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("user.get_info(uuid)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.update_date", "m.update_user", "m.insert_date", "m.insert_user", "m.delete_date", "m.delete_user",
                "m.id, m.description",
                "m.uuid","m.email, m.secret, m.fullname, m.address, m.birthdate, m.phone",

                "m.id_profile","m.id_parent", "m.id_country", "m.id_language",

                "ar2.description as e_profile",
                "COALESCE(CONCAT(bd.business_name,' (',m.description,')'),m.description) as e_parent",
                "ar3.description as e_country",
                "ar1.description as e_language",
            ])
            ->add_join("LEFT JOIN base_user u ON m.id_parent = u.id")
            ->add_join("LEFT JOIN app_business_data bd ON m.id = bd.id_user")
            ->add_join("LEFT JOIN app_array ar1 ON m.id_language = ar1.id_pk AND ar1.type='language'")
            ->add_join("LEFT JOIN base_array ar2 ON m.id_profile = ar2.id_pk AND ar2.type='profile'")
            ->add_join("LEFT JOIN app_array ar3 ON m.id_country = ar3.id AND ar3.type='country'")
            ->add_and("m.uuid='$userUuid'")
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id", "id_profile", "id_parent", "id_country", "id_language"]);
        return $r[0] ?? [];
    }

    public function getAllUsersHierarchyIds(): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("get_all_hierarchy")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id", "m.id_parent"])
            ->select()->sql()
        ;
        $r = $this->query($sql);
        $this->mapFieldsToInt($r, ["id", "id_parent"]);
        return $r;
    }

    public function setAuthService(AuthService $authService): self
    {
        $this->authService = $authService;
        return $this;
    }

    public function getOwnerOfIdUser(string $idUser): array
    {
        /**
         * @var HierarchyComponent $hier
         */
        $hier = CF::getInstanceOf(HierarchyComponent::class);
        return $hier->getTopParent($idUser, $this->getAllUsersHierarchyIds());
    }

    public function getIdOwnerByIdUser(string $idUser): int
    {
        $owner = $this->getOwnerOfIdUser($idUser);
        return (int) $owner["id"];
    }

    public function getChildrenIdsByIdUser(string $idUser): array
    {
        /**
         * @var HierarchyComponent $hier
         */
        $hier = CF::getInstanceOf(HierarchyComponent::class);
        return $hier->getChildrenIds($idUser, $this->getAllUsersHierarchyIds());
    }

    public function isIdUserEnabledBusinessOwner(int $idUser): bool
    {
        $idProfile = UserProfileType::BUSINESS_OWNER;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("user.isIdUserBusinessOwner")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.is_enabled = 1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id = $idUser")
            ->add_and("m.id_profile=$idProfile")
            ->select()->sql()
        ;
        return (bool) $this->query($sql, 0, 0);
    }

    public function isIdUserBusinessOwner(int $idUser): bool
    {
        $idprofile = UserProfileType::BUSINESS_OWNER;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("user.is_owner")
            ->set_table("$this->table as m")
            ->set_getfields(["m.id"])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.id=$idUser")
            ->add_and("m.id_profile=$idprofile")
            ->select()->sql()
        ;
        return (bool) $this->query($sql, 0, 0);
    }
}
