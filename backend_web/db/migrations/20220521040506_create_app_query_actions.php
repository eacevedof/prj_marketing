<?php

declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppQueryActions extends AbsMigration
{
    private string $tablename = "app_query_actions";

    public function up(): void
    {
        $this->_create_table();
    }

    private function _create_table(): void
    {
        $table = $this->table("{$this->tablename}", [
            "engine" => "MyISAM",
            "collation" => "utf8_general_ci",
            "id" => false,
            "primary_key" => ["id"]
        ]);

        $table->addColumn("insert_platform", "string", [
            "limit" => 3,
            "default" => 1,
            "null" => true,
            "comment" => "base_array.type=platform (id_pk) desde que plataforma se ha realizado la creacion, mobile, web, api",
        ])
        ->addColumn("insert_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
            "comment" => "creador (no necesariamente en base_user) puede ser un proceso ETL",
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP",
            "comment" => "datetime de la creacion",
        ])
        ->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("id_query", "integer", [
            "limit" => 11,
            "null" => false
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
            "comment" => "csv, pdf, json, xml, ..."
        ])
        ->addColumn("params", "string", [
            //"limit" => 21570, esto lo admite pero por precaución lo dejo en 20
            "limit" => 20000,
            "null" => true,
            "comment" => "payload de columnas"
        ])
        ->create();

        $table
            ->addIndex(["id", "id_query"], ["name" => "id__id_query_idx"])
            ->addIndex(["id_query"], ["name" => "id_query_idx"])
            ->update();
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
