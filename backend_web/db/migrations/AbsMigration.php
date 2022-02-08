<?php
declare(strict_types=1);
namespace Migrations;

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table;

abstract class AbsMigration extends AbstractMigration
{
    protected function add_sysfields(Table $table): void
    {
        $table->addColumn("processflag", "string", [
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
        ]);
    }
}