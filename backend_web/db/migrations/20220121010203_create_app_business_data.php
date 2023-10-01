<?php

declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppBusinessData extends AbsMigration
{
    private string $tablename = "app_business_data";

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
            "id" => false,
            "primary_key" => ["id"]
        ]);

        $this->add_sysfields_min($table);

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("id_user", "integer", [
            "limit" => 11,
            "null" => false
        ])
        ->addColumn("id_tz", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 1 //app_array.tz 1 = UTC
        ])
        ->addColumn("business_name", "string", [
            "limit" => 250,
            "null" => false
        ])
        ->addColumn("slug", "string", [
            "limit" => 250,
            "null" => false
        ])
        ->addColumn("user_logo_1", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("user_logo_2", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("user_logo_3", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_favicon", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("head_bgcolor", "string", [
            "limit" => 10,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("head_color", "string", [
            "limit" => 10,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("head_bgimage", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("body_bgcolor", "string", [
            "limit" => 10,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("body_color", "string", [
            "limit" => 10,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("body_bgimage", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_business", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_social_fb", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_social_ig", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_social_twitter", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("url_social_tiktok", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("disabled_date", "datetime", [
            "null" => true,
            "comment" => "datetime de la deshabilitacion",
        ])
        ->addColumn("disabled_user", "string", [
            "limit" => 15,
            "null" => true,
            "comment" => "deshabilitador (no necesariamente en base_user) puede ser un proceso ETL",
        ])
        ->addColumn("disabled_reason", "string", [
            "limit" => 1000,
            "null" => true,
            "comment" => "why is disabled",
        ])
        ->create();

        $table->addIndex(["delete_date"], ["name" => "delete_date_idx"])
            ->addIndex(["uuid"], ["name" => "uuid_idx"])
            ->addIndex(["id_user"], ["name" => "id_user_idx"])
            ->addIndex(["slug"], ["name" => "slug_idx"])
            ->addIndex(["id","uuid"], ["name" => "id__uuid_idx"])
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
