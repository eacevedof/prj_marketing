<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class CreateBaseArray extends AbstractMigration
{
    /*
     * ``
`update_platform`
`update_user`
`update_date`
`delete_platform`
`delete_user`
`delete_date`
`cru_csvnote`
`is_erpsent`
`is_enabled`
`i`
`id`
`uuid``code_erp`
`type`
`id_tosave`
`description`
`order_by`
     * */
    public function up(): void
    {
        $this->table("base_arra", ["collation" => "utf8_general_ci"])
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
                "default" => "current_timestamp()",
            ])
            ->addColumn("update_platform", "string", [
                "limit" => 3
            ])
            ->addColumn("update_user", "string", [
                "limit" => 15,
                "default" => null
            ])
            ->addColumn("update_date", "datetime", [
                "default" => "current_timestamp()",
            ])
            ->addColumn("delete_platform", "string", [
                "limit" => 3
            ])
            ->addColumn("delete_user", "string", [
                "limit" => 15
            ])
            ->addColumn("delete_date", "datetime", [
                "default" => "current_timestamp()",
            ])
            ->addColumn("cru_csvnote", "string", [
                "limit" => 500
            ])
            ->create();
    }

    public function down(): void
    {

    }
}
