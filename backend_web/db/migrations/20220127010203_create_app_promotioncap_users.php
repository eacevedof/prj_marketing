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
            "limit" => 30,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("name2", "string", [
            "limit" => 30,
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
        ->addColumn("is_mailing", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
            "comment" => "0: no, 1: yes",
        ])
        ->addColumn("is_terms", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 1,
            "comment" => "0: no, 1: yes",
        ])
        ->create();

        $table->addIndex(["delete_date"], ["name"=>"delete_date_idx"])
            ->addIndex(["uuid"], ["name"=>"uuid_idx"])
            ->addIndex(["id_owner"], ["name"=>"id_owner_idx"])
            ->addIndex(["id_promotion"], ["name"=>"id_promotion_idx"])
            ->addIndex(["id_language"], ["name"=>"id_language_idx"])
            ->addIndex(["id_country"], ["name"=>"id_country_idx"])
            ->addIndex(["email"], ["name"=>"email_idx"])
            ->addIndex(["is_mailing"], ["name"=>"is_mailing_idx"])
            ->addIndex(["name1"], ["name"=>"name1_idx"])
            ->addIndex(["id","uuid"], ["name"=>"id__uuid_idx"])
            ->update()
        ;

        $this->_initial_load();
    }

    private function _initial_load(): void { }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
