<?php

namespace App\Restrict\BusinessAttributes\Domain;

use App\Shared\Domain\Repositories\AppRepository;
use App\Shared\Infrastructure\Factories\DbFactory as DbF;

final class BusinessAttributeRepository extends AppRepository
{
    private array $joins = [];

    public function __construct()
    {
        $this->componentMysql = DbF::getMysqlInstanceByEnvConfiguration();
        $this->table = "app_business_attribute";
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

    public function getSpacePageByIdUser(int $idUser): array
    {
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("business_attribute.get_spacepage_by_iduser(iduser)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.id",
                "m.id_user",
                "m.attr_key",
                "m.attr_value"
            ])
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.attr_key LIKE '%space_%'")
            ->add_and("m.id_user=$idUser")
            ->select()->sql()
        ;
        $result = $this->componentMysql->query($sql);
        $this->mapFieldsToInt($result, ["id", "id_user"]);
        return $result;
    }

    public function getSpacePageByBusinessSlug(string $businessSlug): array
    {
        $businessSlug = $this->_getSanitizedString($businessSlug);
        $sql = $this->_getQueryBuilderInstance()
            ->set_comment("business_attribute.get_spacepage_by_businessslug(businesslug)")
            ->set_table("$this->table as m")
            ->set_getfields([
                "m.id",
                "m.id_user",
                "m.attr_key",
                "m.attr_value"
            ])
            ->add_join("INNER JOIN app_business_data AS bd ON m.id_user = bd.id_user")
            ->add_and("m.delete_date IS NULL")
            ->add_and("m.attr_key LIKE '%space_%'")
            ->add_and("bd.delete_date IS NULL")
            ->add_and("bd.slug='$businessSlug'")
            ->select()->sql()
        ;
        $result = $this->componentMysql->query($sql);
        $this->mapFieldsToInt($result, ["id", "id_user"]);
        return $result;
    }

}
