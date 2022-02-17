<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseArray extends AbsMigration
{
    private string $tablename = "base_array";

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

        $this->add_sysfields($table);

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
        ->addColumn("id_pk", "string", [
            "limit" => 25,
            "null" => true,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
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
        $sqls = [
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('1','profile','1','root','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('2','profile','2','sys admin','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('3','profile','3','business owner','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('4','profile','4','business manager','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('7','platform','0','etl','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('8','platform','1','web','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('9','platform','2','mobile','100');",
            "INSERT INTO base_array ( id,type,id_pk,description,order_by) VALUES ('10','platform','3','console','100');",
        ];
        foreach ($sqls as $sql)
            $this->execute($sql);
    }

    public function down(): void
    {
        $this->table($this->tablename)->drop()->save();
    }
}
