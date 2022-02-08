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
            "default" => null,
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
        ])
        ->addColumn("email", "string", [
            "limit" => 100,
            "null" => false,
            "default" => null,
        ])
        ->addColumn("secret", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("phone", "string", [
            "limit" => 20,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("fullname", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("address", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("birthdate", "datetime", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("geo_location", "string", [
            "limit" => 500,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_parent", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_gender", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_nationality", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_country", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_language", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("path_picture", "string", [
            "limit" => 100,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("id_profile", "integer", [
            "limit" => 11,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("token_reset", "string", [
            "limit" => 250,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("log_attempts", "integer", [
            "limit" => 5,
            "null" => true,
            "default" => null,
        ])
        ->addColumn("date_validated", "datetime", [
            "null" => true,
            "default" => null,
        ])
        ->addColumn("is_notifiable", "string", [
            "limit" => 4,
            "null" => true,
            "default" => null,
        ])
        ->create();
    }

    private function _initial_load(): void
    {
        $array = [
            [
                //secret: eaf
                "id"=>"1", "email"=>"root@email.com", "description"=>"Root One", "secret"=>"$2y$10\$BEClm.fzRU2shGk5nMLGRe4f0JnkXofGMBkLZ6sC86f8/aeetCMhC",
                "fullname" => "Root One", "uuid"=>"U00001", "id_gender" => "1", "id_nationality" => "1", "id_country" => "69",
                "id_language" => "2", "id_profile" => "1",
            ],
        ];

        foreach ($array as $item) {
            list(
                "id"=>$id, "email"=>$email, "description"=>$description, "secret"=>$secret, "fullname"=>$fullname,
                "uuid"=>$uuid, "id_gender"=>$idgender, "id_nationality"=>$idnationality, "id_country"=>$idcountry,
                "id_language"=>$idlanguage, "id_profile"=>$idprofile
            ) = $item;

            $sql = "
            INSERT INTO {$this->tablename} 
            (id, `email`,`description`, secret, fullname, uuid, id_gender, id_nationality, id_country, id_language, id_profile)
            VALUES($id, '$email', '$description', '$secret','$fullname','$uuid','$idgender','$idnationality','$idcountry','$idlanguage','$idprofile')
            ";
            $this->execute($sql);
        }
    }

    public function down(): void
    {

    }
}

