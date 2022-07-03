<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotioncapSubscriptions extends AbsMigration
{
    private string $tablename = "app_promotioncap_subscriptions";

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
            "limit" => 35,
            "null" => true,
            "comment" => "posible md5"
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
        ->addColumn("is_test", "integer", [
            "limit" => 2,
            "null" => false,
            "default" => 0,
            "comment" => "0:No, 1:Yes"
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

        $table->addIndex(["delete_date"], ["name"=>"delete_date_idx"])
            ->addIndex(["uuid"], ["name"=>"uuid_idx"])
            ->addIndex(["id_owner"], ["name"=>"id_owner_idx"])
            ->addIndex(["id_promotion"], ["name"=>"id_promotion_idx"])
            ->addIndex(["id_promouser"], ["name"=>"id_promouser_idx"])
            ->addIndex(["id","uuid"], ["name"=>"id__uuid_idx"])
            ->update()
        ;

        $this->_initial_load();
    }

    private function _initial_load(): void { }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
