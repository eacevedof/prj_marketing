<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseArray extends AbsMigration
{
    public function up(): void
    {
        $this->_create_table();
        $this->_initial_load();
    }
    
    private function _create_table(): void
    {
        $table = $this->table("base_arrayx", [
            "collation" => "utf8_general_ci",
            "id"=> false,
            "primary_key" => ["id"]
        ]);

        $this->add_sysfields($table);

        $table->addColumn("id", "integer", [
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
            "limit" => 5,
            "default"=>100
        ])
        ->create();

        $table
            ->changeColumn("id", "integer", ["identity" => true])
            ->save()
        ;
    }
    
    private function _initial_load(): void
    {
        $array = [
            ["id"=>"1", "type"=>"profile", "description"=>"root", "order_by"=>100],
            ["id"=>"2", "type"=>"profile", "description"=>"sys admin", "order_by"=>100],
            ["id"=>"3", "type"=>"profile", "description"=>"business owner", "order_by"=>100],
            ["id"=>"4", "type"=>"profile", "description"=>"business manager", "order_by"=>100],
        ];

        foreach ($array as $item) {
            list("id"=>$id, "type"=>$type, "description"=>$description, "order_by"=>$orderby) = $item;
            $sql = "
            INSERT INTO base_arrayx (id, `type`, `description`, order_by)
            VALUES($id, '$type', '$description', $orderby)
            ";
            $this->execute($sql);
        }

        $array = [
            ["code_erp" => 0, "type"=>"platform", "description"=>"etl", "order_by"=>100],
            ["code_erp" => 1, "type"=>"platform", "description"=>"web", "order_by"=>100],
            ["code_erp" => 2, "type"=>"platform", "description"=>"mobile", "order_by"=>100],
        ];

        foreach ($array as $item) {
            list("code_erp"=>$coderp, "type"=>$type, "description"=>$description, "order_by"=>$orderby) = $item;
            $sql = "
            INSERT INTO base_arrayx (code_erp, `type`, `description`, order_by)
            VALUES('$coderp','$type', '$description', $orderby)
            ";
            $this->execute($sql);
        }
    }

    public function down(): void
    {

    }
}
