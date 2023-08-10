<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link eduardoaf.com
 * @name App\Shared\Domain\Repositories\App\ArrayRepository
 * @file ArrayRepository.php v1.0.0
 * @date 29-11-2018 19:00 SPAIN
 * @observations
 */

namespace App\Shared\Domain\Repositories\App;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Traits\PicklistTrait;
use App\Picklist\Domain\Enums\AppArrayType as Types;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class ArrayRepository extends AppRepository
{
    use PicklistTrait;

    private array $result = [];

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
    }

    public function getPromotionTypesByIdOwner(?int $idOwner): array
    {
        if (!$idOwner) {
            $idOwner = 0;
        }
        $type = Types::PROMOTION;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_promotion_types")
            ->set_table("app_array as m")
            ->set_getfields(["m.id_pk as id", "m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type = '$type'")
            ->add_and("(m.id_owner=-1 OR m.id_owner = $idOwner)")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        $this->mapFieldsToInt($this->result, ["id"]);
        return $this->_getKeyValueArray(["id","description"]);
    }

    public function getLanguages(): array
    {
        $type = Types::LANGUAGE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_languages")
            ->set_table("app_array as m")
            ->set_getfields(["m.id_pk as id", "m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type= '$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        $this->mapFieldsToInt($this->result, ["id"]);
        return $this->_getKeyValueArray(["id","description"]);
    }

    public function getCountries(): array
    {
        $type = Types::COUNTRY;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_countries")
            ->set_table("app_array as m")
            ->set_getfields(["m.id","m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        $this->mapFieldsToInt($this->result, ["id"]);
        return $this->_getKeyValueArray(["id", "description"]);
    }

    public function getGenders(): array
    {
        $type = Types::GENDER;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_genders")
            ->set_table("app_array as m")
            ->set_getfields(["m.id_pk as id", "m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        $this->mapFieldsToInt($this->result, ["id"]);
        return $this->_getKeyValueArray(["id", "description"]);
    }

    public function getTimezones(): array
    {
        $type = Types::TIMEZONE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_tzs")
            ->set_table("app_array as m")
            ->set_getfields(["m.id_pk as id", "m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_owner=-1")
            ->add_orderby("m.order_by")
            ->add_orderby("m.description")
            ->select()->sql()
        ;
        $this->result = $this->query($sql);
        $this->mapFieldsToInt($this->result, ["id"]);
        return $this->_getKeyValueArray(["id","description"]);
    }

    public function getTimezoneIdByDescription(string $description): int
    {
        $type = Types::TIMEZONE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_tzs")
            ->set_table("app_array as m")
            ->set_getfields(["m.id_pk as id"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.description='$description'")
            ->add_and("m.id_owner=-1")
            ->select()->sql()
        ;
        return (int) $this->query($sql, 0, 0);
    }

    public function getTimezoneDescriptionByIdPk(int $idPk): ?string
    {
        $type = Types::TIMEZONE;
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.get_tzs")
            ->set_table("app_array as m")
            ->set_getfields(["m.description"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.type='$type'")
            ->add_and("m.id_pk=$idPk")
            ->add_and("m.id_owner=-1")
            ->select()->sql()
        ;
        return $this->query($sql, 0, 0);
    }

    public function exists(int $pkFieldValue, string $arrayType, string $pkFieldName = "id"): bool
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("apparrayrepo.exists")
            ->set_table("app_array as m")
            ->set_getfields(["m.id"])
            ->add_and("m.is_enabled=1")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.$pkFieldName=$pkFieldValue")
            ->add_and("m.type='$arrayType'")
            ->select()->sql()
        ;
        return (bool) $this->query($sql, 0, 0);
    }

}
