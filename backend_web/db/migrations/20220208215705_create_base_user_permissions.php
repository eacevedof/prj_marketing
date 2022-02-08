<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseUserPermissions extends AbsMigration
{
    private string $tablename = "base_user_permissionsx";

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

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("id_user", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("json_rw", "string", [
            "limit" => 2000,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
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

    }
}

