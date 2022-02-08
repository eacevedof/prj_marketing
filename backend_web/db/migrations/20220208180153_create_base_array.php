<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseArray extends AbsMigration
{
    public function up(): void
    {
        $table = $this->table("base_array", [
            "collation" => "utf8_general_ci",
            "id"=> false,
            "primary_key" => ["id"]
        ]);

        $this->add_sysfields($table);

        $table->addColumn("id", "integer", [
                "limit" => 11
            ])
            ->addColumn("uuid", "string", [
                "limit" => 50
            ])
            ->addColumn("code_erp", "string", [
                "limit" => 25
            ])
            ->addColumn("type", "string", [
                "limit" => 15
            ])
            ->addColumn("id_tosave", "string", [
                "limit" => 25
            ])
            ->addColumn("description", "string", [
                "limit" => 250
            ])
            ->addColumn("order_by", "integer", [
                "limit" => 5
            ])
            ->create();
    }

    public function down(): void
    {

    }
}
