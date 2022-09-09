<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class AddRaffleConfiguration extends AbsMigration
{
    private string $tablename = "app_promotion";

    public function up(): void
    {
        $this->_add_columns();
        $this->_initial_load();
    }

    private function _add_columns(): void
    {
        $table = $this->table("{$this->tablename}");

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => false,
        ])
        ->addColumn("id_owner", "integer", [
            "limit" => 11,
            "null" => false
        ])


        ->save();

    }

    private function _initial_load(): void { }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
