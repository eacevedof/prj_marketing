<?php
declare(strict_types=1);
use Migrations\AbsMigration;

final class CreateBaseUser extends AbsMigration
{
    private string $tablename = "base_userx";

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
            "default" => true,
        ])
        ->addColumn("code_erp", "string", [
            "limit" => 25,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("description", "string", [
            "limit" => 250,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("email", "string", [
            "limit" => 100,
            "null" => false,
            "default" => true,
        ])
        ->addColumn("secret", "string", [
            "limit" => 100,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("phone", "string", [
            "limit" => 20,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("fullname", "string", [
            "limit" => 100,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("address", "string", [
            "limit" => 250,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("birthdate", "datetime", [
            "null" => true,
            "default" => true,
        ])
        ->addColumn("geo_location", "string", [
            "limit" => 500,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_parent", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_gender", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_nationality", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_country", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_language", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("path_picture", "string", [
            "limit" => 100,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("id_profile", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("token_reset", "string", [
            "limit" => 250,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("log_attempts", "integer", [
            "limit" => 5,
            "null" => true,
            "default" => true,
        ])
        ->addColumn("date_validated", "datetime", [
            "null" => true,
            "default" => true,
        ])
        ->addColumn("is_notificable", "string", [
            "limit" => 4,
            "null" => true,
            "default" => true,
        ])
        ->create();
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
            INSERT INTO {$this->tablename} (id, `type`, `description`, order_by)
            VALUES($id, '$type', '$description', $orderby)
            ";
            //$this->execute($sql);
        }
    }

    public function down(): void
    {

    }
}

