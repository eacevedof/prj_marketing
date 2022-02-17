<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotionSubscriptions extends AbsMigration
{
    private string $tablename = "app_promotion_subscriptions";

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

        $this->add_fixed_fields($table);

        $table->addColumn("id_promotion", "integer", [
            "limit" => 11,
            "null" => false,
        ])
        ->addColumn("id_promouser", "integer", [
            "limit" => 11,
            "null" => false,
        ])
        ->addColumn("date_subscription", "datetime", [
            "null" => false,
            "comment" => "fecha de suscripción",
        ])
        ->addColumn("date_confirm", "datetime", [
            "null" => true,
            "default" => null,
            "comment" => "fecha-hora en la que se confirma",
        ])
        ->addColumn("date_execution", "datetime", [
            "null" => true,
            "default" => null,
            "comment" => "fecha-hora en la que se ejecuta",
        ])
        ->addColumn("code_execution", "string", [
            "limit" => 15,
            "null" => false
        ])
        ->addColumn("exec_user", "integer", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("subs_status", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 1,
            "comment" => "0:subscribed,1:confirmed,2:executed,3:canceled"
        ])
        ->addColumn("remote_ip", "string", [
            "limit" => 15,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("notes", "string", [
            "limit" => 300,
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
        $this->table($this->tablename)->drop()->save();
    }
}
