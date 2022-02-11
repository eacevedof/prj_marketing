<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotionUi extends AbsMigration
{
    private string $tablename = "app_promotion_uix";

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
        ->addColumn("input_email", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 1,
            "comment" => "se mostrara el campo de email"
        ])
        ->addColumn("pos_email", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
            "comment" => "la posición required?"
        ])
        ->addColumn("input_name1", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 1,
        ])
        ->addColumn("pos_name1", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_name2", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_name2", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_language", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_language", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_country", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_country", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_phone1", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_phone1", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_birthdate", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_birthdate", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_gender", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_gender", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
        ])
        ->addColumn("input_address", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("pos_address", "integer", [
            "limit" => 3,
            "null" => false,
            "default" => 100,
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
