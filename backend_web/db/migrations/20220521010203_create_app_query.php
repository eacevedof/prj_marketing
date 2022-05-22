<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppQuery extends AbsMigration
{
    private string $tablename = "app_query";

    public function up(): void
    {
        $this->_create_table();
    }

    private function _create_table(): void
    {
        $this->table("{$this->tablename}", [
            //"engine" => "MyISAM",
            "collation" => "utf8_general_ci",
            "id"=> false,
            "primary_key" => ["id"]
        ])
        ->addColumn("insert_platform", "string", [
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
            "limit" => 10,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("module", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
        ])
        ->addColumn("query", "text", [
            "null" => true,
            "comment" => "null true pq para el borrado se puede marcar a null y menguar el tamaño de la tabla"
        ])
        ->addColumn("total", "integer", [
            "limit" => 10,
            "identity" => true,
        ])
        ->addIndex(["id","uuid"], ["name"=>"id__uuid_idx"])
        ->addIndex(["uuid"], ["name"=>"uuid_idx"])
        ->create();
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
