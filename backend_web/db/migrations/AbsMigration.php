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
            "comment" => "en procesos etl indica estados de preprocesamiento"
        ])
        ->addColumn("insert_platform", "string", [
            "limit" => 3,
            "default" => 1,
            "null" => true,
            "comment" => "base_array.type=platform (id_pk) desde que plataforma se ha realizado la creacion, mobile, web, api",
        ])
        ->addColumn("insert_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
            "comment" => "creador (no necesariamente en base_user) puede ser un proceso ETL",
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP",
            "comment" => "datetime de la creacion",
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
            "update"=> "CURRENT_TIMESTAMP"
        ])
        ->addColumn("delete_platform", "string", [
            "limit" => 3,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("delete_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
        ])
        ->addColumn("delete_date", "datetime", [
            "null" => true,
            "default" => null,
            "null" => true,
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
            "comment" => "en procesos etl indica si se ha exportado"
        ])
        ->addColumn("is_enabled", "string", [
            "limit" => 3,
            "null" => true,
            "default" => 1,
            "comment" => "bloquea por completo el archivo"
        ])
        ->addColumn("i", "integer", [
            "limit" => 11,
            "default"=> null,
            "null" => true,
            "comment" => "en procesos de etl con disp offline el autoid en la máquina"
        ]);
    }

    protected function add_sysfields_min(Table $table): void
    {
        $table->addColumn("processflag", "string", [
            "limit" => 5,
            "default" => null,
            "null" => true,
            "comment" => "en procesos etl indica estados de preprocesamiento"
        ])
        ->addColumn("insert_platform", "string", [
            "limit" => 3,
            "default" => 1,
            "null" => true,
            "comment" => "desde que plataforma se ha realizado la creacion, mobile, web, api",
        ])
        ->addColumn("insert_user", "string", [
            "limit" => 15,
            "default" => null,
            "null" => true,
            "comment" => "creador (no necesariamente en base_user) puede ser un proceso ETL",
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP",
            "comment" => "datetime de la creacion",
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
            "null" => true,
        ])
        ->addColumn("delete_date", "datetime", [
            "null" => true,
            "default" => null,
            "null" => true,
        ]);
    }

    protected function add_fixed_fields(Table $table): void
    {
        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("id_owner", "integer", [
            "limit" => 11,
            "null" => false,
            "comment" => "base_user.id"
        ])
        ->addColumn("code_erp", "string", [
            "limit" => 25,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ]);
    }

}