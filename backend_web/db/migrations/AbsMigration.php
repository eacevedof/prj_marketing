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
            "default" => null,
            "null" => true,
        ])
        ->addColumn("insert_platform", "string", [
            "limit" => 3,
            "default" => 1,
            "null" => true,
        ])
        ->addColumn("insert_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP",
        ])
        ->addColumn("update_platform", "string", [
            "limit" => 3,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("update_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("update_date", "datetime", [
            "default" => "CURRENT_TIMESTAMP",
            "null" => true,
        ])
        ->addColumn("delete_platform", "string", [
            "limit" => 3,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("delete_user", "string", [
            "limit" => 15,
            "default" => null,
        ])
        ->addColumn("delete_date", "datetime", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("cru_csvnote", "string", [
            "limit" => 500,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("is_erpsent", "string", [
            "limit" => 3,
            "default" =>0,
            "null" => true,
        ])
        ->addColumn("is_enabled", "string", [
            "limit" => 3,
            "null" => true,
            "default" => 1
        ])
        ->addColumn("i", "integer", [
            "limit" => 11,
            "default"=> null,
            "null" => true,
        ]);
    }
}