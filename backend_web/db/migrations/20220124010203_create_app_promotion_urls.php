<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotionUrls extends AbsMigration
{
    private string $tablename = "app_promotion_urls";

    public function up(): void
    {
        $this->_create_table();
        $this->_initial_load();
    }

    private function _create_table(): void
    {
        $table = $this->table("{$this->tablename}", [
            "engine" => "MyISAM",
            "collation" => "utf8_general_ci",
            "id"=> false,
            "primary_key" => ["id"]
        ]);

        $this->add_sysfields_min($table);

        $this->add_fixed_fields($table);

        $table->addColumn("id_promotion", "integer", [
            "limit" => 11,
            "null" => false,
        ])
        ->addColumn("id_type", "integer", [
            "limit" => 11,
            "null" => false,
            "comment" => "app_array.type='promotion' ticktock, fb, twitter, instagram, other",
        ])
        ->addColumn("url_design", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
            "comment" => "la url del diseño en la red social"
        ])
        ->addColumn("url_promotion", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
            "comment" => "la url de la promoción en la red social"
        ])
        ->addColumn("notes", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
            "comment" => "notas relevantes, por ejemplo, hay que pasar el token=xxx"
        ])
        ->addColumn("is_active", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 1,
            "comment" => "para el futuro, indicará si hay que desactivar capturas desde esa publicacion"
        ])
        ->create();

        $table->addIndex(["delete_date"], ["name"=>"delete_date_idx"])
            ->addIndex(["uuid"], ["name"=>"uuid_idx"])
            ->addIndex(["id_owner"], ["name"=>"id_owner_idx"])
            ->addIndex(["description"], ["name"=>"description_idx"])
            ->addIndex(["id_promotion"], ["name"=>"id_promotion_idx"])
            ->addIndex(["id_type"], ["name"=>"id_type_idx"])
            ->addIndex(["id", "uuid"], ["name"=>"id__uuid_idx"])
            ->update()
        ;
    }

    private function _initial_load(): void
    {
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
