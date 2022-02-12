<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotionUser extends AbsMigration
{
    private string $tablename = "app_promotion_userx";

    public function up(): void
    {
        $this->_create_table();
        $this->_initial_load();
    }

    private function _create_table(): void
    {
        $table = $this->table("{$this->tablename}", [
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
        ->addColumn("id_language", "integer", [
            "limit" => 11,
            "null" => false,
            "default" => 1,
            "comment" => "app_array.type='language' 1: english, 2:spanish",
        ])
        ->addColumn("id_country", "integer", [
            "limit" => 11,
            "null" => false,
            "default" => 1,
            "comment" => "app_array.type='country'",
        ])
        ->addColumn("url_promotion", "string", [
            "limit" => 1000,
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
    }

    private function _initial_load(): void
    {
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
