<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppIpUntracked extends AbsMigration
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
            "default" => null,
        ])
        ->addColumn("whois", "string", [
            "limit" => 200,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("comment", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("is_enabled", "integer", [
            "limit" => 4,
            "null"=>true,
            "default"=> 1,
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
