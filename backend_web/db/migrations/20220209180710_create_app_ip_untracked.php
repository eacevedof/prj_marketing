<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class AppIpUntracked extends AbsMigration
{
    private string $tablename = "app_ip_untrackedx";

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

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP",
        ])
        ->addColumn("update_date", "datetime", [
            "default" => "CURRENT_TIMESTAMP",
            "null" => true,
            "update"=> "CURRENT_TIMESTAMP"
        ])
        ->addColumn("remote_ip", "string", [
            "limit" => 100,
            "null" => false,
        ])
        ->addColumn("country", "string", [
            "limit" => 50,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("whois", "string", [
            "limit" => 200,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("comment", "string", [
            "limit" => 250,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("is_enabled", "integer", [
            "limit" => 4,
            "default"=>1,
            "null"=>true,
        ])
        ->create();
    }

    private function _initial_load(): void
    {
    }

    public function down(): void
    {

    }
}
