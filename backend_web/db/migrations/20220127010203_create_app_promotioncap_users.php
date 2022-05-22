<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotioncapUsers extends AbsMigration
{
    private string $tablename = "app_promotioncap_users";

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
        ->addColumn("id_language", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type='language' 1: english, 2:spanish",
        ])
        ->addColumn("id_country", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type=country",
        ])
        ->addColumn("phone1", "string", [
            "limit" => 20,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("email", "string", [
            "limit" => 100,
            "null" => false,
            "comment" => "siempre obligatorio",
        ])
        ->addColumn("birthdate", "datetime", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("name1", "string", [
            "limit" => 15,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("name2", "string", [
            "limit" => 15,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_gender", "integer", [
            "limit" => 2,
            "null" => true,
            "default" => null,
            "comment" => "app_array.type=gender, id_pk:0: female, 1: male, 2:neutral",
        ])
        ->addColumn("address", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
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
