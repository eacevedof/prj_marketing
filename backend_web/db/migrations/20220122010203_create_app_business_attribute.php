<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppBusinessAttribute extends AbsMigration
{
    private string $tablename = "app_business_attribute";

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

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("id_user", "integer", [
            "limit" => 11,
            "null" => false,
            "comment" => "base_user.id"
        ])
        ->addColumn("attr_key", "string", [
            "limit" => 250,
            "null" => false,
        ])
        ->addColumn("attr_value", "string", [
            "limit" => 2000,
            "null" => true,
            "default" => null,
        ])
        ->create();

        $table->addIndex(["delete_date"], ["name"=>"delete_date_idx"])
            ->addIndex(["id_user"], ["name"=>"id_user_idx"])
            ->addIndex(["attr_key"], ["name"=>"attr_key_idx"])
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

