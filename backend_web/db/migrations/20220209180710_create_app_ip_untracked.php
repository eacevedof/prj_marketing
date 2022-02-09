<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class AppIpUntracked extends AbsMigration
{
    private string $tablename = "app_ip_untrackedx";

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

        $table->addColumn("id", "integer", [
            "limit" => 11,
            "identity" => true,
        ])
        ->addColumn("uuid", "string", [
            "limit" => 50,
            "null" => true,
        ])
        ->addColumn("code_erp", "string", [
            "limit" => 25,
            "null" => true,
        ])
        ->addColumn("type", "string", [
            "limit" => 15,
            "null" => true,
        ])
        ->addColumn("id_tosave", "string", [
            "limit" => 25,
            "null" => true,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
        ])
        ->addColumn("id_owner", "integer", [
            "limit" => 11,
            "default"=>-1,
            "null" => false,
        ])
        ->addColumn("order_by", "integer", [
            "limit" => 5,
            "default"=>100,
            "null" => true,
        ])
        ->create();
    }

    private function _initial_load(): void
    {
        $array = [
            ["code_erp" => "en", "type"=>"language", "description"=>"English"],
            ["code_erp" => "es", "type"=>"language", "description"=>"Spanish"],
            ["code_erp" => "nl", "type"=>"language", "description"=>"Dutch"],
            ["code_erp" => "pap", "type"=>"language", "description"=>"Papiaments"],
        ];

        foreach ($array as $item) {
            list("code_erp"=>$coderp, "type"=>$type, "description"=>$description) = $item;
            $sql = "
            INSERT INTO {$this->tablename} (code_erp, `type`, `description`)
            VALUES('$coderp','$type', '$description')
            ";
            $this->execute($sql);
        }
    }

    public function down(): void
    {

    }
}
