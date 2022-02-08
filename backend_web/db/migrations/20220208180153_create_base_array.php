<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseArray extends AbsMigration
{
    public function up(): void
    {
        $this->table("base_arra", ["collation" => "utf8_general_ci", "id"=> false, "primary_key" => ["id"]])
            ->addColumn("processflag", "string", [
                "limit" => 5,
                "default" => null
            ])
            ->addColumn("insert_platform", "string", [
                "limit" => 3,
                "default" => 1
            ])
            ->addColumn("insert_user", "string", [
                "limit" => 15,
                "default" => null
            ])
            ->addColumn("insert_date", "datetime", [
                "default" => "CURRENT_TIMESTAMP",
            ])
            ->addColumn("update_platform", "string", [
                "limit" => 3
            ])
            ->addColumn("update_user", "string", [
                "limit" => 15,
                "default" => null
            ])
            ->addColumn("update_date", "datetime", [
                "default" => "CURRENT_TIMESTAMP",
            ])
            ->addColumn("delete_platform", "string", [
                "limit" => 3
            ])
            ->addColumn("delete_user", "string", [
                "limit" => 15
            ])
            ->addColumn("delete_date", "datetime", [
                "default" => "CURRENT_TIMESTAMP",
            ])
            ->addColumn("cru_csvnote", "string", [
                "limit" => 500
            ])
            ->addColumn("is_erpsent", "string", [
                "limit" => 3,
                "default" =>0
            ])
            ->addColumn("is_enabled", "string", [
                "limit" => 3,
                "default" => 1
            ])
            ->addColumn("i", "integer", [
                "limit" => 11
            ])
            ->addColumn("id", "integer", [
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
