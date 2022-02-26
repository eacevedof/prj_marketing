<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotion extends AbsMigration
{
    private string $tablename = "app_promotion";

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

        $this->add_sysfields($table);

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("id_owner", "integer", [
            "limit" => 11,
            "null" => false
        ])
        ->addColumn("id_tz", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 1 //app_array.tz 1 = UTC
        ])
        ->addColumn("code_erp", "string", [
            "limit" => 25,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("slug", "string", [
            "limit" => 250,
            "null" => false,
        ])
        ->addColumn("date_from", "datetime", [
            "null" => false,
        ])
        ->addColumn("date_to", "datetime", [
            "null" => false,
        ])
        ->addColumn("content", "string", [
            "limit" => 2000,
            "null" => false,
        ])
        ->addColumn("bgcolor", "string", [
            "limit" => 10,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_xs", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_sm", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_md", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_lg", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_xl", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("bgimage_xxl", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("invested", "decimal", [
            "null" => true,
            "default" => 0,
            "precision" => 10,
            "scale" => 3
        ])
        ->addColumn("returned", "decimal", [
            "null" => true,
            "default" => 0,
            "precision" => 10,
            "scale" => 3
        ])
        ->addColumn("max_confirmed", "integer", [
            "limit" => 4,
            "null" => false,
            "default" => 0,
        ])
        ->addColumn("is_drawable", "integer", [
            "limit" => 2,
            "null" => true,
            "default" => 0,
        ])
        ->addColumn("is_cumulative", "integer", [
            "limit" => 2,
            "null" => true,
            "default" => 0,
        ])
        ->addColumn("tags", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("notes", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("num_viewed", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 0,
            "comment" => "metricas de trazabilidad, veces vista"
        ])
        ->addColumn("num_subscribed", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 0
        ])
        ->addColumn("num_confirmed", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 0
        ])
        ->addColumn("num_executed", "integer", [
            "limit" => 5,
            "null" => false,
            "default" => 0
        ])
        ->create();
    }

    private function _initial_load(): void { }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
