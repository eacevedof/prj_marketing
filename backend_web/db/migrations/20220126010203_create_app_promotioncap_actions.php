<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateAppPromotioncapActions extends AbsMigration
{
    private string $tablename = "app_promotioncap_actions";

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

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("id_promotion", "integer", [
            "limit" => 11,
            "null" => false,
        ])
        ->addColumn("id_promouser", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null
        ])
        ->addColumn("id_type", "integer", [
            "limit" => 2,
            "null" => false,
            "comment" => "app_array.type='promotion-steps' viewed, subscribed, confirmed, executed, expired",
        ])
        ->addColumn("url_req", "string", [
            "limit" => 300,
            "null" => true,
            "default" => false
        ])
        ->addColumn("url_ref", "string", [
            "limit" => 300,
            "null" => true,
            "default" => null
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
            "default" => null
        ])
        ->addColumn("insert_date", "datetime", [
            "null" => true,
            "default" => "CURRENT_TIMESTAMP"
        ])
        ->create();
    }

    private function _initial_load(): void {}

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
